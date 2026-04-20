<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/* Entidad que representa las suscripciones de profesores a los distintos planes que tienen junto a sus características ya sean
   el acceso a funcionalidades o el límite de cursos que puede crear cada profesor. */
#[ORM\Entity]
#[ORM\Table(name: 'suscripcion_profesor')]
class SuscripcionProfesor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Profesor que tiene la suscripción */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $profesor = null;

    /* Plan actual del profesor que por defecto es básico */
    #[ORM\Column(length: 50)]
    private string $plan = 'basico';

    /* Estado de la suscripción marcado como pendiente hasta ser aprobada */
    #[ORM\Column(length: 50)]
    private string $estado = 'pendiente';

    /* Número máximo de cursos que puede crear que por defecto es 1 para el plan básico */
    #[ORM\Column]
    private int $limiteCursos = 1;

    /* Fecha de solicitud de suscripción */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $fechaSolicitud = null;

    /* Fecha de aprobación de la suscripción */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $fechaAprobacion = null;

    /* Plan que el profesor ha solicitado cambiar */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $planSolicitado = null;

    /* Tipo de solicitud */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $tipoSolicitud = null;

    public function __construct()
    {
        $this->fechaSolicitud = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getProfesor(): ?User { return $this->profesor; }

    public function setProfesor(?User $profesor): static
    {
        $this->profesor = $profesor;
        return $this;
    }

    public function getPlan(): string { return $this->plan; }

    public function setPlan(string $plan): static
    {
        $this->plan = $plan;
        return $this;
    }

    public function getEstado(): string { return $this->estado; }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
        return $this;
    }

    public function getLimiteCursos(): int { return $this->limiteCursos; }

    public function setLimiteCursos(int $limiteCursos): static
    {
        $this->limiteCursos = $limiteCursos;
        return $this;
    }

    public function getFechaSolicitud(): ?\DateTimeImmutable { return $this->fechaSolicitud; }

    public function setFechaSolicitud(\DateTimeImmutable $fechaSolicitud): static
    {
        $this->fechaSolicitud = $fechaSolicitud;
        return $this;
    }

    public function getFechaAprobacion(): ?\DateTimeImmutable { return $this->fechaAprobacion; }

    public function setFechaAprobacion(?\DateTimeImmutable $fechaAprobacion): static
    {
        $this->fechaAprobacion = $fechaAprobacion;
        return $this;
    }

    public function getPlanSolicitado(): ?string { return $this->planSolicitado; }

    public function setPlanSolicitado(?string $planSolicitado): static
    {
        $this->planSolicitado = $planSolicitado;
        return $this;
    }

    public function getTipoSolicitud(): ?string { return $this->tipoSolicitud; }

    public function setTipoSolicitud(?string $tipoSolicitud): static
    {
        $this->tipoSolicitud = $tipoSolicitud;
        return $this;
    }
}