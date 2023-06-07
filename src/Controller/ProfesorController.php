<?php

namespace App\Controller;

use App\Entity\CursoAsignatura;
use App\Entity\Periodo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfesorController extends AbstractController
{

    #[Route('/', name: 'app_profesor_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $periodos = $entityManager->getRepository(Periodo::class)->findAll();

        return $this->render('profesor/inicio.html.twig', [

            'profesor' => $this->getUser()
        ]);
    }


    #[Route('/inicio', name: 'app_profesor')]
    public function inicio(): Response
    {
        // dump($this->getUser());
        // die();
        return $this->render('profesor/index.html.twig', [
            'controller_name' => 'ProfesorController',
        ]);
    }

    #[Route('/data-profesor/{id}', name: 'data_profesor')]

    public function dataProfesor(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $usuario = $entityManager->getRepository(User::class)->find($id);
        $periodos = $entityManager->getRepository(Periodo::class)->findAll();
        $data_periodos = array_map(
            function($p) { return $p->toArray(); },
            $periodos
        );
        
        if($usuario->getRol() == User::ROLE_ADMIN){
            $cursoAsignaturas = $entityManager->getRepository(CursoAsignatura::class)->findAll();
        }else{
            $cursoAsignaturas = $usuario->getAsignaturas()->toArray();
        }

        $data_curso_asignaturas = array_map(
            function($ca) { return $ca->toArray(); },
            $cursoAsignaturas
        );

        return new JsonResponse([
            'status' => true,
            'profesor' => $usuario->toArray(),
            'cursoAsignaturas' => $data_curso_asignaturas,    
            'periodos' => $data_periodos
          ]);
    }


}
