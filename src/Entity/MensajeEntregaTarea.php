<?php

namespace App\Entity;

use App\Repository\MensajeEntregaTareaRepository;
use Doctrine\ORM\Mapping as ORM;

/* Mensajes del mini chat entre alumno y profesor dentro de una entrega */
#[ORM\Entity(repositoryClass: MensajeEntregaTareaRepository::class)]
class MensajeEntregaTarea
{
    /* ID autogenerado */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Entrega a la que pertenece el mensaje */
    #[ORM\ManyToOne(inversedBy: 'mensajes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?EntregaTarea $entrega = null;

    /* Usuario autor del mensaje */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $autor = null;

    /* Contenido del mensaje */
    #[ORM\Column(type: 'text')]
    private ?string $contenido = null;

    /* Fecha de creación del mensaje */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $fechaCreacion = null;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntrega(): ?EntregaTarea
    {
        return $this->entrega;
    }

    public function setEntrega(?EntregaTarea $entrega): static
    {
        $this->entrega = $entrega;
        return $this;
    }

    public function getAutor(): ?User
    {
        return $this->autor;
    }

    public function setAutor(?User $autor): static
    {
        $this->autor = $autor;
        return $this;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): static
    {
        $this->contenido = $contenido;
        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeImmutable
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeImmutable $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }
}