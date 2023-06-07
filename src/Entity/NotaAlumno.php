<?php

namespace App\Entity;

use App\Repository\NotaAlumnoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotaAlumnoRepository::class)]
class NotaAlumno
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notasAlumnos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Periodo $periodo = null;

    #[ORM\Column]
    private ?float $calificacion = null;

    #[ORM\ManyToOne(inversedBy: 'notasAlumnos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Asignatura $asignatura = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Nota $notaTemplate = null;

    #[ORM\ManyToOne(inversedBy: 'notas')]
    private ?User $alumno = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriodo(): ?Periodo
    {
        return $this->periodo;
    }

    public function setPeriodo(?Periodo $periodo): self
    {
        $this->periodo = $periodo;

        return $this;
    }

    public function getCalificacion(): ?float
    {
        return $this->calificacion;
    }

    public function setCalificacion(float $calificacion): self
    {
        $this->calificacion = $calificacion;

        return $this;
    }

    public function getAsignatura(): ?Asignatura
    {
        return $this->asignatura;
    }

    public function setAsignatura(?Asignatura $asignatura): self
    {
        $this->asignatura = $asignatura;

        return $this;
    }

    public function getNotaTemplate(): ?Nota
    {
        return $this->notaTemplate;
    }

    public function setNotaTemplate(?Nota $notaTemplate): self
    {
        $this->notaTemplate = $notaTemplate;

        return $this;
    }

    public function getAlumno(): ?User
    {
        return $this->alumno;
    }

    public function setAlumno(?User $alumno): self
    {
        $this->alumno = $alumno;

        return $this;
    }

    public function toArray(){

        return  [
            'id' => $this->getId(),
            'periodo' => $this->periodo->getId(),
            'calificacion' => $this->getCalificacion(),
            'asignatura' => $this->getAsignatura()->toArray(),
            'notaTemplate' => $this->getNotaTemplate()->getId(),
            'porcentaje' => $this->getNotaTemplate()->getPorcentaje()
        ];

    }
}
