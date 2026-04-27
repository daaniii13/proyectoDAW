<?php

namespace App\Entity;

use App\Repository\CursoRepository;
use Doctrine\ORM\Mapping as ORM;

/* Entidad que representa la tabla de cursos. */
#[ORM\Entity(repositoryClass: CursoRepository::class)]
class Curso
{
    /* Clave primaria generada automáticamente por la base de datos */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Título del curso limitado a 255 caracteres */
    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    /* Descripción detallada del curso, sin límite de longitud */
    #[ORM\Column(type: 'text')]
    private ?string $descripcion = null;

    /* Nivel del curso */
    #[ORM\Column(length: 100)]
    private ?string $nivel = null;

    /* Modalidad del curso */
    #[ORM\Column(length: 100)]
    private ?string $modalidad = null;

    /* Idioma principal del curso */
    #[ORM\Column(length: 5)]
    private ?string $idioma = 'es';

    /* Duración del curso */
    #[ORM\Column(length: 100)]
    private ?string $duracion = null;

    /* Precio del curso */
    #[ORM\Column(length: 50)]
    private ?string $precio = null;

    /* Estado del curso que va a estar en activo por defecto */
    #[ORM\Column(length: 50)]
    private ?string $estado = 'activo';

    /* Fecha de creación inmutable, no se puede modificar una vez asignada */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $fechaCreacion = null;

    /* Profesor que imparte el curso, debe estar asignado obligatoriamente */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $profesor = null;

    /* URL de la imagen de portada del curso */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bannerUrl = null;

    /* URL del mapa o ubicación del curso */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mapaUrl = null;

    /* Constructor de cursos con la fecha actual y con el curso en activo */
    public function __construct()
    {
        $this->fechaCreacion = new \DateTimeImmutable();
        $this->estado = 'activo';
        $this->idioma = 'es';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNivel(): ?string
    {
        return $this->nivel;
    }
    public function setNivel(string $nivel): static
    {
        $this->nivel = $nivel;
        return $this;
    }

    public function getModalidad(): ?string
    {
        return $this->modalidad;
    }
    public function setModalidad(string $modalidad): static
    {
        $this->modalidad = $modalidad;
        return $this;
    }

    public function getIdioma(): ?string
    {
        return $this->idioma;
    }
    public function setIdioma(string $idioma): static
    {
        $this->idioma = $idioma;
        return $this;
    }

    public function getDuracion(): ?string
    {
        return $this->duracion;
    }
    public function setDuracion(string $duracion): static
    {
        $this->duracion = $duracion;
        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }
    public function setPrecio(string $precio): static
    {
        $this->precio = $precio;
        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }
    public function setEstado(string $estado): static
    {
        $this->estado = $estado;
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

    public function getProfesor(): ?User
    {
        return $this->profesor;
    }
    public function setProfesor(?User $profesor): static
    {
        $this->profesor = $profesor;
        return $this;
    }

    public function getBannerUrl(): ?string
    {
        return $this->bannerUrl;
    }
    public function setBannerUrl(?string $bannerUrl): static
    {
        $this->bannerUrl = $bannerUrl;
        return $this;
    }

    public function getMapaUrl(): ?string
    {
        return $this->mapaUrl;
    }
    public function setMapaUrl(?string $mapaUrl): static
    {
        $this->mapaUrl = $mapaUrl;
        return $this;
    }
}