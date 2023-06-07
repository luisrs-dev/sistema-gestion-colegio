<?php

namespace App\Controller;

use App\Entity\Alumno;
use App\Entity\ArchivoCargaMasiva;
use App\Entity\User;
use App\Form\AlumnoType;
use App\Form\ArchivoCargaMasivaType;
use App\Repository\AlumnoRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/alumno')]
class AlumnoController extends AbstractController
{

    private $adminUrlGenerator;
    private $entityManager;
    private $passwordHasher;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

    }


    // #[Route('/', name: 'app_alumno_index', methods: ['GET'])]
    // public function index(AlumnoRepository $alumnoRepository): Response
    // {
    //     return $this->render('alumno/index.html.twig', [
    //         'alumnos' => $alumnoRepository->findAll(),
    //     ]);
    // }

    // #[Route('/new', name: 'app_alumno_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, AlumnoRepository $alumnoRepository): Response
    // {
    //     $alumno = new Alumno();
    //     $form = $this->createForm(AlumnoType::class, $alumno);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $alumnoRepository->save($alumno, true);

    //         return $this->redirectToRoute('app_alumno_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('alumno/new.html.twig', [
    //         'alumno' => $alumno,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_alumno_show', methods: ['GET'])]
    // public function show(Alumno $alumno): Response
    // {
    //     dump($alumno);
    //     die();
    //     return $this->render('alumno/show.html.twig', [
    //         'alumno' => $alumno,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_alumno_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Alumno $alumno, AlumnoRepository $alumnoRepository): Response
    // {
    //     $form = $this->createForm(AlumnoType::class, $alumno);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $alumnoRepository->save($alumno, true);

    //         return $this->redirectToRoute('app_alumno_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('alumno/edit.html.twig', [
    //         'alumno' => $alumno,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_alumno_delete', methods: ['POST'])]
    // public function delete(Request $request, Alumno $alumno, AlumnoRepository $alumnoRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$alumno->getId(), $request->request->get('_token'))) {
    //         $alumnoRepository->remove($alumno, true);
    //     }

    //     return $this->redirectToRoute('app_alumno_index', [], Response::HTTP_SEE_OTHER);
    // }

    #[Route('/alumnos/carga-masiva', name: 'carga_masiva_alumnos')]
    public function uploadAlumnos(Request $request, SluggerInterface $slugger,  PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $archivoCargaMasiva = new ArchivoCargaMasiva();
        $form = $this->createForm(ArchivoCargaMasivaType::class, $archivoCargaMasiva);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $archivoCargaMasivaData = $form->get('path')->getData();
            $curso = $form->get('curso')->getData();

            if ($archivoCargaMasivaData) {
                $originalFilename = pathinfo($archivoCargaMasivaData->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$archivoCargaMasivaData->guessExtension();
                // Mover el archivo al directorio donde son almacenados
                try {
                    $archivoCargaMasivaData->move(
                        $this->getParameter('archivo_carga_masiva_directory'),
                        $newFilename
                    );

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
               
                // getParameter() -> Parámetros son configurados en services.yaml
                $pathFile = $this->getParameter('archivo_carga_masiva_directory') . '/' . $newFilename;
                $archivoCargaMasiva->setPath($pathFile);
                $archivoCargaMasiva->setNombre($originalFilename);
                $archivoCargaMasiva->setFechaCreacion(new DateTime('now'));
                $em->persist($archivoCargaMasiva);
                // $em->flush();
                
                $alumnos = $this->extraerAlumnos($pathFile);
                
                foreach ($alumnos as $alumno) {
                    $user = new User();
                    $user->setUsername($alumno['username']);
                    $user->setFullname($alumno['nombre']);
                    $user->setPassword($alumno['password']);
                    $user->setRol(User::ROLE_ALUMNO);
                    $user->setCurso($curso);

                    $hashedPassword = $this->passwordHasher->hashPassword(
                        $user,
                        $user->plainPassword
                    );
                    $user->setPassword($hashedPassword);
                    $em->persist($user);
                }
                // $em->flush();
            }
            
            $em->flush();

        // return $this->redirect($this->adminUrlGenerator->setController(PrePaymentCrudController::class)->generateUrl());
        return $this->renderForm('alumno/carga-masiva.html.twig',[
            'form' => $form
        ]);

        }
        return $this->renderForm('alumno/carga-masiva.html.twig',[
            'form' => $form
        ]);
    }

    public function extraerAlumnos($xlsxFile){

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($xlsxFile);
        $dataFromSpreadsheet = $this->createDataFromSpreadsheet($spreadsheet);
        return $this->extraerAbonos($dataFromSpreadsheet);
    }

    protected function createDataFromSpreadsheet($spreadsheet)
    {
        $dataSpreadsheet = [];
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $dataSpreadsheet[$worksheetTitle] = [
                'columnNames' => [],
                'columnValues' => [],
            ];
            foreach ($worksheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                $dataSpreadsheet[$worksheetTitle]['columnValues'][$rowIndex] = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop over all cells, even if it is not set
                foreach ($cellIterator as $cell) {
                    $dataSpreadsheet[$worksheetTitle]['columnNames'][] = $cell->getCalculatedValue();
                    $dataSpreadsheet[$worksheetTitle]['columnValues'][$rowIndex][] = $cell->getCalculatedValue();
                }
            }
        }
        return $dataSpreadsheet;
    }

    public function extraerAbonos($dataSpreadsheet){
        $alumnos = [];
        foreach ($dataSpreadsheet as $pageSpreadsheet) {
            foreach ($pageSpreadsheet['columnValues'] as  $index=>$columnValue) {
                // Se separa rut de guión
                $rutNoGuion = explode('-', $columnValue[1])[0];
                // Se extraen los últimos 4 digitos para clave
                $password = substr($rutNoGuion, -4);

                $alumnos[] = [
                    'username' => $columnValue[1],
                    'nombre' => implode(" ",array_slice($columnValue, 2, 4)),
                    'password' => $password
                ];
        }
        return $alumnos;
    }
    }
}
