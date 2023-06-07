<?php

namespace App\Controller;

use App\Entity\CursoAsignatura;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CursoAsignaturaController extends AbstractController
{
    #[Route('/curso/asignatura', name: 'app_curso_asignatura')]
    public function index(): Response
    {
        return $this->render('curso_asignatura/index.html.twig', [
            'controller_name' => 'CursoAsignaturaController',
        ]);
    }

    #[Route('/data-curso-asignatura/{id}', name: 'app_curso_asignatura')]
    public function dataCursoAsignatura(EntityManagerInterface $entityManager, $id): Response
    {
        $cursoAsignatura = $entityManager->getRepository(CursoAsignatura::class)->find($id);
        return new JsonResponse($cursoAsignatura->toArray());
    }


}
