<?php

namespace App\Controller;

use App\Entity\Alumno;
use App\Entity\Asignatura;
use App\Entity\Curso;
use App\Entity\CursoAsignatura;
use App\Entity\Nota;
use App\Entity\NotaAlumno;
use App\Entity\Periodo;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;


class NotasController extends AbstractController
{
    #[Route('/notas', name: 'app_notas')]
    public function index(): Response
    {
        return $this->render('notas/index.html.twig', [
            'controller_name' => 'NotasController',
        ]);
    }

    #[Route('/data-alumno/{id}', name: 'data_alumno')]
    public function dataAlumno(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $alumno = $entityManager->getRepository(User::class)->find($id);
        dump($alumno);
        dump($alumno->getNotas()->toArray());
        // foreach ($alumno->getNotas() as $key => $nota) {
        //     dump($nota);
        // }
        die();
    }



    #[Route('/registro-nota', name: 'registro_nota')]
    public function registroNota(Request $request, EntityManagerInterface $entityManager): Response
    {
        $alumno = $entityManager->getRepository(User::class)->find($request->query->all()['alumnoId']);
        $notaTemplate = $entityManager->getRepository(Nota::class)->find($request->query->all()['notaId']);
        $asignatura = $entityManager->getRepository(Asignatura::class)->find($request->query->all()['asignatura']);
        $periodo = $entityManager->getRepository(Periodo::class)->findOneBy(['nombre' => 'Primer Semestre']);
        $notaIngresada = $request->query->all()['notaInput'];

        // Si nota existe se actualiza, si no se crea una nueva
        $notaxAlumno = $entityManager->getRepository(NotaAlumno::class)->findOneBy(['alumno' => $alumno, 'notaTemplate' => $notaTemplate, 'asignatura' => $asignatura]);

        if ($notaxAlumno) {

            $notaxAlumno->setCalificacion($notaIngresada);
            $entityManager->persist($notaxAlumno);
        } else {
            $notaAlumno = new NotaAlumno();
            $notaAlumno->setAlumno($alumno);
            $notaAlumno->setAsignatura($asignatura);
            $notaAlumno->setCalificacion($notaIngresada);
            $notaAlumno->setPeriodo($periodo);
            $notaAlumno->setNotaTemplate($notaTemplate);
            $entityManager->persist($notaAlumno);
        }
        $entityManager->flush();


        return new JsonResponse([
            'status' => true,
            'msg' => 'Nota registrada',
            'notasAlumno' => array_map(
                function($alumnoNota) { return $alumnoNota->toArray(); },
                $notaAlumno->getAlumno()->getNotas()->toArray()
            )
        ]);
        
        // dump($notaxAlumno);

        // die();
    }

    #[Route('/eliminar-nota', name: 'eliminar_nota')]
    public function eliminarNota(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {

            $alumnoId = $entityManager->getRepository(User::class)->find($request->request->get('alumnoId'));
            $notaId = $entityManager->getRepository(Nota::class)->find($request->request->get('notaId'));
    
            $alumno = $entityManager->getRepository(User::class)->find($alumnoId);
            $notaTemplate = $entityManager->getRepository(Nota::class)->find($notaId);
    
            $notaxAlumno = $entityManager->getRepository(NotaAlumno::class)->findOneBy(['alumno' => $alumno, 'notaTemplate' => $notaTemplate]);
            $entityManager->remove($notaxAlumno);
            $entityManager->flush();
    
            return new JsonResponse([
                'status' => true,
                'msg' => 'Nota eliminada',
                'notasAlumno' => array_map(
                    function($alumnoNota) { return $alumnoNota->toArray(); },
                    $alumno->getNotas()->toArray()
                )    
            ]);
            //code...
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JsonResponse([
                'status' => false,
                'msg' => $e->getMessage()
            ]);

        }


    }



    #[Route('/registro-notas', name: 'registro_notas')]
    public function registroNotas(Request $request, EntityManagerInterface $entityManager): Response
    {

        // dump($request->query->all()['alumnos']);

        // foreach ($$request->query->all()['data'] as $key => $value) {
        //     dump($value);
        // }
        // die();

        $periodo = $entityManager->getRepository(Periodo::class)->findOneBy(['nombre' => 'Primer Semestre']);

        $entityAsignatura = $entityManager->getRepository(Asignatura::class)->find($request->query->all()['asignatura']['id']);

        $entityCurso = $entityManager->getRepository(Curso::class)->find($request->query->all()['curso']['id']);


        $entityCursoAsignatura = $entityManager->getRepository(CursoAsignatura::class)->findOneBy(['curso' => $entityCurso, 'asignatura' => $entityAsignatura]);


        $alumnos = $entityManager->getRepository(Alumno::class)->findAll();
        // $alumnos = $request->query->all()['alumnos'];
        foreach ($alumnos as $key => $alumno) {
            if (isset($alumno['notas'])) {
                $entityAlumno = $entityManager->getRepository(Alumno::class)->find($alumno['id']);
                foreach ($alumno['notas'] as $key => $nota) {

                    $entityNota = $entityManager->getRepository(Nota::class)->find($nota['id']);

                    $notaAlumno = new NotaAlumno();
                    $notaAlumno->setAlumno($entityAlumno);
                    $notaAlumno->setAsignatura($entityAsignatura);
                    $notaAlumno->setCalificacion($nota['calificacion']);
                    $notaAlumno->setPeriodo($periodo);
                    $notaAlumno->setNotaTemplate($entityNota);
                    $entityManager->persist($notaAlumno);
                }
            }
        }
        $entityManager->flush();
        return new JsonResponse([
            'status' => true,
            'msg' => 'Notas registradas'
        ]);
    }

    #[Route('/descargar-notas', name: 'decargar_notas')]
    public function descargarNotas(Request $request, EntityManagerInterface $entityManager): Response
    {
        $alumnos = $request->query->all()['alumnos'];
        // dump($alumnos);
        // die();

        $html = $this->render('notas/formato_pdf.html.twig', [
            'alumnos' => $alumnos,
            // 'produccion' => $tarjetaProduccion,
            // 'tarjetasDetalle' => $tarjetasDetalle,
            'document_root' => $_SERVER['DOCUMENT_ROOT'],
            'dev' => false
        ])->getContent();
        return new Response($html);
    }
}
