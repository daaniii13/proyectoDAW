<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/* Esta entidad registra qué recursos ha visto cada usuario y te permite rastrear el progreso del estudiante a través del contenido del curso. */
#[ORM\Entity]
#[ORM\Table(name: 'recurso_visto')]
class RecursoVisto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Usuario que visualizó el recurso */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $usuario = null;

    /* Recurso que fue visualizado */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?RecursoCurso $recurso = null;

    /* Fecha y hora cuando el usuario vio el recurso que no se puede cambiar */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $fechaVisto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getRecurso(): ?RecursoCurso
    {
        return $this->recurso;
    }

    public function setRecurso(?RecursoCurso $recurso): static
    {
        $this->recurso = $recurso;
        return $this;
    }

    public function getFechaVisto(): ?\DateTimeImmutable
    {
        return $this->fechaVisto;
    }

    public function setFechaVisto(\DateTimeImmutable $fechaVisto): static
    {
        $this->fechaVisto = $fechaVisto;
        return $this;
    }
}
