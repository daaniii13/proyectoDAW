<?php

namespace App\Entity;

use App\Repository\EntregaTareaRepository;
use Doctrine\ORM\Mapping as ORM;

// Entidad que representa la entrega de una tarea por parte de un estudiante
#[ORM\Entity(repositoryClass: EntregaTareaRepository::class)]
class EntregaTarea
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // ID autogenerado
    #[ORM\Column]
    private ?int $id = null;

    // Relación con la tarea a la que pertenece la entrega
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')] // Si se borra la tarea, se borra la entrega
    private ?TareaCurso $tarea = null;

    // Relación con el usuario (estudiante) que realiza la entrega
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')] // Si se borra el usuario, se borra la entrega
    private ?User $estudiante = null;

    // Ruta o nombre del archivo entregado
    #[ORM\Column(length: 255)]
    private ?string $archivoEntrega = null;

    // Comentario opcional del estudiante al entregar la tarea
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comentario = null;

    // Fecha en la que se realiza la entrega
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fechaEntrega = null;

    public function __construct()
    {
        // Por defecto, la fecha de entrega se establece al momento de crear el objeto
        $this->fechaEntrega = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarea(): ?TareaCurso
    {
        return $this->tarea;
    }

    public function setTarea(?TareaCurso $tarea): static
    {
        $this->tarea = $tarea;
        return $this;
    }

    public function getEstudiante(): ?User
    {
        return $this->estudiante;
    }

    public function setEstudiante(?User $estudiante): static
    {
        $this->estudiante = $estudiante;
        return $this;
    }

    public function getArchivoEntrega(): ?string
    {
        return $this->archivoEntrega;
    }

    public function setArchivoEntrega(string $archivoEntrega): static
    {
        $this->archivoEntrega = $archivoEntrega;
        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): static
    {
        $this->comentario = $comentario;
        return $this;
    }

    public function getFechaEntrega(): ?\DateTimeInterface
    {
        return $this->fechaEntrega;
    }

    public function setFechaEntrega(\DateTimeInterface $fechaEntrega): static
    {
        $this->fechaEntrega = $fechaEntrega;
        return $this;
    }
}