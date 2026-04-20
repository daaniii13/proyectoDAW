<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/* Entidad que representa los comentarios en las entregas de tareas de los usuarios. */
#[ORM\Entity]
class EntregaComentario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Entrega a la que pertenece el comentario */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?EntregaTarea $entrega = null;

    /* Usuario que escribió el comentario */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $autor = null;

    /* Texto del mensaje o comentario, se almacena como TEXT para no limitar longitud */
    #[ORM\Column(type: 'text')]
    private string $mensaje;

    /* Fecha y hora del comentario */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fecha;

    public function __construct()
    {
        $this->fecha = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getEntrega(): ?EntregaTarea { return $this->entrega; }
    public function setEntrega(?EntregaTarea $e): static { $this->entrega = $e; return $this; }
    public function getAutor(): ?User { return $this->autor; }
    public function setAutor(?User $a): static { $this->autor = $a; return $this; }
    public function getMensaje(): string { return $this->mensaje; }
    public function setMensaje(string $m): static { $this->mensaje = $m; return $this; }
    public function getFecha(): \DateTimeInterface { return $this->fecha; }
}