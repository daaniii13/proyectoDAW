<?php

namespace App\Entity;

use App\Repository\EntregaTareaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/* Entrega de una tarea por parte de un estudiante y posterior correción del profesor */
#[ORM\Entity(repositoryClass: EntregaTareaRepository::class)]
class EntregaTarea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Tarea a la que pertenece esta entrega */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?TareaCurso $tarea = null;

    /* Estudiante que realiza la entrega */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $estudiante = null;

    /* Archivo entregado, nombre del fichero */
    #[ORM\Column(length: 255)]
    private ?string $archivoEntrega = null;

    /* Comentario opcional del estudiante */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comentario = null;

    /* Fecha en la que se realiza la entrega */
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fechaEntrega = null;

    /* Estado de la revisión */
    #[ORM\Column(length: 30)]
    private string $estadoRevision = 'pendiente';

    /* Nota asignada por el profesor, null si todavía no se ha corregido */
    #[ORM\Column(nullable: true)]
    private ?int $nota = null;

    /* Comentario del profesor tras la corrección */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comentarioProfesor = null;

    /* Fecha en la que se corrige la entrega */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $fechaRevision = null;

    /* Mensajes del mini chat de esta entrega */
    #[ORM\OneToMany(mappedBy: 'entrega', targetEntity: MensajeEntregaTarea::class, orphanRemoval: true)]
    #[ORM\OrderBy(['fechaCreacion' => 'ASC'])]
    private Collection $mensajes;

    public function __construct()
    {
        /* Al crear la entrega, se asigna la fecha actual y se pone pendiente de revisión */
        $this->fechaEntrega = new \DateTime();
        $this->estadoRevision = 'pendiente';
        $this->mensajes = new ArrayCollection();
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

    public function getEstadoRevision(): string
    {
        return $this->estadoRevision;
    }

    public function setEstadoRevision(string $estadoRevision): static
    {
        $this->estadoRevision = $estadoRevision;
        return $this;
    }

    public function getNota(): ?int
    {
        return $this->nota;
    }

    public function setNota(?int $nota): static
    {
        $this->nota = $nota;
        return $this;
    }

    public function getComentarioProfesor(): ?string
    {
        return $this->comentarioProfesor;
    }

    public function setComentarioProfesor(?string $comentarioProfesor): static
    {
        $this->comentarioProfesor = $comentarioProfesor;
        return $this;
    }

    public function getFechaRevision(): ?\DateTimeInterface
    {
        return $this->fechaRevision;
    }

    public function setFechaRevision(?\DateTimeInterface $fechaRevision): static
    {
        $this->fechaRevision = $fechaRevision;
        return $this;
    }

    public function getMensajes(): Collection
    {
        return $this->mensajes;
    }

    public function addMensaje(MensajeEntregaTarea $mensaje): static
    {
        if (!$this->mensajes->contains($mensaje)) {
            $this->mensajes->add($mensaje);
            $mensaje->setEntrega($this);
        }

        return $this;
    }

    public function removeMensaje(MensajeEntregaTarea $mensaje): static
    {
        if ($this->mensajes->removeElement($mensaje)) {
            if ($mensaje->getEntrega() === $this) {
                $mensaje->setEntrega(null);
            }
        }

        return $this;
    }
}