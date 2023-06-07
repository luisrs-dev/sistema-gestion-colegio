<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name : 'user')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_PROFESOR = 'ROLE_PROFESOR';
    const ROLE_ALUMNO = 'ROLE_ALUMNO';

    const ROLES = [
        'Administrador' => self::ROLE_ADMIN,
        'Usuario' => self::ROLE_USER,
        'Profesor' => self::ROLE_PROFESOR,
        'Alumno' => self::ROLE_ALUMNO
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $rol = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $fullname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    public $plainPassword = '';

    #[ORM\OneToMany(mappedBy: 'profesor', targetEntity: CursoAsignatura::class)]
    private Collection $asignaturas;

    #[ORM\ManyToOne(inversedBy: 'alumnos')]
    private ?Curso $curso = null;

    #[ORM\OneToMany(mappedBy: 'alumno', targetEntity: NotaAlumno::class)]
    private Collection $notas;

    public function __construct()
    {
        $this->asignaturas = new ArrayCollection();
        $this->notas = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_unique([self::ROLE_USER, $this->rol]);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function __toString()
    {
        return $this->username . ' ' . $this->fullname;
    }

    /**
     * @return Collection<int, CursoAsignatura>
     */
    public function getAsignaturas(): Collection
    {
        return $this->asignaturas;
    }

    public function addAsignatura(CursoAsignatura $asignatura): self
    {
        if (!$this->asignaturas->contains($asignatura)) {
            $this->asignaturas->add($asignatura);
            $asignatura->setProfesor($this);
        }

        return $this;
    }

    public function removeAsignatura(CursoAsignatura $asignatura): self
    {
        if ($this->asignaturas->removeElement($asignatura)) {
            // set the owning side to null (unless already changed)
            if ($asignatura->getProfesor() === $this) {
                $asignatura->setProfesor(null);
            }
        }

        return $this;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): self
    {
        $this->curso = $curso;

        return $this;
    }

    /**
     * @return Collection<int, NotaAlumno>
     */
    public function getNotas(): Collection
    {
        return $this->notas;
    }

    public function addNota(NotaAlumno $nota): self
    {
        if (!$this->notas->contains($nota)) {
            $this->notas->add($nota);
            $nota->setAlumno($this);
        }

        return $this;
    }

    public function removeNota(NotaAlumno $nota): self
    {
        if ($this->notas->removeElement($nota)) {
            // set the owning side to null (unless already changed)
            if ($nota->getAlumno() === $this) {
                $nota->setAlumno(null);
            }
        }

        return $this;
    }
    
   public function getRol(): string
   {
       return $this->rol ? $this->rol : self::ROLE_USER;
   }

    public function setRol(string $rol)
    {
        $this->rol = $rol;

        return $this;
    }

    public function toArray(){
        return  [
            'id' => $this->getId(),
            'nombre' => $this->getFullname(),
            'notas' => array_map(function($nota){
                return $nota->toArray();
                }, $this->getNotas()->toArray()
            ),
            'rol' => $this->getRol()
        ];
    }
}
