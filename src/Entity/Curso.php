<?php

namespace App\Entity;

use App\Repository\CursoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursoRepository::class)]
class Curso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\OneToMany(mappedBy: 'curso', targetEntity: CursoAsignatura::class)]
    private Collection $cursoAsignaturas;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable:true)]
    private ?\DateTimeInterface $fechaCreacion = null;

    #[ORM\OneToMany(mappedBy: 'curso', targetEntity: NotaAlumno::class)]
    private Collection $notasAlumnos;

    #[ORM\OneToMany(mappedBy: 'curso', targetEntity: User::class)]
    private Collection $alumnos;

    #[ORM\ManyToOne(inversedBy: 'curso')]
    private ?ArchivoCargaMasiva $archivoCargaMasiva = null;

    public function __construct()
    {
        $this->cursoAsignaturas = new ArrayCollection();
        $this->notasAlumnos = new ArrayCollection();
        $this->alumnos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection<int, CursoAsignatura>
     */
    public function getCursoAsignaturas(): Collection
    {
        return $this->cursoAsignaturas;
    }

    public function addCursoAsignatura(CursoAsignatura $cursoAsignatura): self
    {
        if (!$this->cursoAsignaturas->contains($cursoAsignatura)) {
            $this->cursoAsignaturas->add($cursoAsignatura);
            $cursoAsignatura->setCurso($this);
        }

        return $this;
    }

    public function removeCursoAsignatura(CursoAsignatura $cursoAsignatura): self
    {
        if ($this->cursoAsignaturas->removeElement($cursoAsignatura)) {
            // set the owning side to null (unless already changed)
            if ($cursoAsignatura->getCurso() === $this) {
                $cursoAsignatura->setCurso(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nombre;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function toArray(){
        return  [
            'id' => $this->getId(),
            'nombre' => $this->getNombre()
        ];
    }

    /**
     * @return Collection<int, NotaAlumno>
     */
    public function getNotasAlumnos(): Collection
    {
        return $this->notasAlumnos;
    }

    public function addNotasAlumno(NotaAlumno $notasAlumno): self
    {
        if (!$this->notasAlumnos->contains($notasAlumno)) {
            $this->notasAlumnos->add($notasAlumno);
            $notasAlumno->setCurso($this);
        }

        return $this;
    }

    public function removeNotasAlumno(NotaAlumno $notasAlumno): self
    {
        if ($this->notasAlumnos->removeElement($notasAlumno)) {
            // set the owning side to null (unless already changed)
            if ($notasAlumno->getCurso() === $this) {
                $notasAlumno->setCurso(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAlumnos(): Collection
    {
        return $this->alumnos;
    }

    public function addAlumno(User $alumno): self
    {
        if (!$this->alumnos->contains($alumno)) {
            $this->alumnos->add($alumno);
            $alumno->setCurso($this);
        }

        return $this;
    }

    public function removeAlumno(User $alumno): self
    {
        if ($this->alumnos->removeElement($alumno)) {
            // set the owning side to null (unless already changed)
            if ($alumno->getCurso() === $this) {
                $alumno->setCurso(null);
            }
        }

        return $this;
    }

    public function getArchivoCargaMasiva(): ?ArchivoCargaMasiva
    {
        return $this->archivoCargaMasiva;
    }

    public function setArchivoCargaMasiva(?ArchivoCargaMasiva $archivoCargaMasiva): self
    {
        $this->archivoCargaMasiva = $archivoCargaMasiva;

        return $this;
    }
}
