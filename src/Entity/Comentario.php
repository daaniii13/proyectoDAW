<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/* Entidad que representa la tabla de comentarios. */
#[ORM\Entity]
class Comentario
{
    /* Clave primaria, su valor lo genera la base de datos automáticamente */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Curso al que pertenece el comentario, no puede quedar sin asignar */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Curso $curso = null;

    /* Usuario que escribió el comentario, tampoco puede quedar sin asignar */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    /* Texto del comentario, se almacena como TEXT para no limitar la longitud */
    #[ORM\Column(type: 'text')]
    private string $contenido;

    /* Indica si el comentario está destacado, por defecto no lo está */
    #[ORM\Column]
    private bool $destacado = false;

    /* Fecha y hora de creación */
    #[ORM\Column]
    private \DateTime $fechaCreacion;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getCurso(): ?Curso { return $this->curso; }
    public function setCurso(?Curso $curso): static { $this->curso = $curso; return $this; }

    public function getUsuario(): ?User { return $this->usuario; }
    public function setUsuario(?User $usuario): static { $this->usuario = $usuario; return $this; }

    public function getContenido(): string { return $this->contenido; }
    public function setContenido(string $contenido): static { $this->contenido = $contenido; return $this; }

    public function isDestacado(): bool { return $this->destacado; }
    public function setDestacado(bool $destacado): static { $this->destacado = $destacado; return $this; }

    public function getFechaCreacion(): \DateTime { return $this->fechaCreacion; }
}