<?php

namespace App\Entity;

use App\Repository\InscripcionRepository;
use Doctrine\ORM\Mapping as ORM;

/* Entidad que representa la inscripción en un curso */
#[ORM\Entity(repositoryClass: InscripcionRepository::class)]
class Inscripcion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Usuario que realiza la inscripción */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $estudiante = null;

    /* Curso al que se inscribe el estudiante */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Curso $curso = null;

    /* Progreso del estudiante en el curso */
    #[ORM\Column]
    private int $progreso = 0;

    /* Estado de la inscripción */
    #[ORM\Column(length: 50)]
    private string $estado = 'pendiente_pago';

    /* Fecha en la que se realiza la inscripción */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $fechaInscripcion = null;

    /* Referencia del pago asociado a la inscripción  */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $referenciaPago = null;

    public function __construct()
    {
        /* Se asigna automáticamente la fecha de inscripción al crear el objeto */
        $this->fechaInscripcion = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(?Curso $curso): static
    {
        $this->curso = $curso;
        return $this;
    }

    public function getProgreso(): int
    {
        return $this->progreso;
    }

    public function setProgreso(int $progreso): static
    {
        $this->progreso = $progreso;
        return $this;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    public function getFechaInscripcion(): ?\DateTimeImmutable
    {
        return $this->fechaInscripcion;
    }

    public function setFechaInscripcion(\DateTimeImmutable $fechaInscripcion): static
    {
        $this->fechaInscripcion = $fechaInscripcion;
        return $this;
    }

    public function getReferenciaPago(): ?string
    {
        return $this->referenciaPago;
    }

    public function setReferenciaPago(?string $referenciaPago): static
    {
        $this->referenciaPago = $referenciaPago;
        return $this;
    }
}