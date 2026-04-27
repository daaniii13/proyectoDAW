<?php

namespace App\Entity;

use App\Repository\TareaCursoRepository;
use Doctrine\ORM\Mapping as ORM;

/* Entidad que representa las tareas que el profesor asigna a los estudiantes y los datos necesarios vinculados a estas */
#[ORM\Entity(repositoryClass: TareaCursoRepository::class)]
class TareaCurso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Curso al que pertenece la tarea */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Curso $curso = null;

    /* Título de la tarea */
    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    /* Descripción de lo que debe hacer el estudiante */
    #[ORM\Column(type: 'text')]
    private ?string $descripcion = null;

    /* Fecha límite para entregar la tarea */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fechaLimite = null;

    /* Archivo de referencia proporcionado por el profesor */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $archivoProfesor = null;

    /* Fecha de creación de la tarea */
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fechaCreacion = null;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): static
    {
        $this->curso = $curso;
        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getFechaLimite(): ?\DateTimeInterface
    {
        return $this->fechaLimite;
    }

    public function setFechaLimite(?\DateTimeInterface $fechaLimite): static
    {
        $this->fechaLimite = $fechaLimite;
        return $this;
    }

    public function getArchivoProfesor(): ?string
    {
        return $this->archivoProfesor;
    }

    public function setArchivoProfesor(?string $archivoProfesor): static
    {
        $this->archivoProfesor = $archivoProfesor;
        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }
}