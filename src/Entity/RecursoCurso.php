<?php

namespace App\Entity;

use App\Repository\RecursoCursoRepository;
use Doctrine\ORM\Mapping as ORM;

/* Entidad que representa los recursos dentro de un curso ya sean en video, PDF o cualquier material educativo con un orden específico de visualización. */
#[ORM\Entity(repositoryClass: RecursoCursoRepository::class)]
class RecursoCurso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Curso al que pertenece el recurso que no puede ser nulo */
    #[ORM\ManyToOne(targetEntity: Curso::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Curso $curso = null;

    /* Título descriptivo del recurso para identificarlo */
    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    /* Tipo de recurso */
    #[ORM\Column(length: 50)]
    private ?string $tipo = null;

    /* Contenido del recurso */
    #[ORM\Column(type: 'text')]
    private ?string $contenido = null;

    /* Orden de aparición del recurso en el curso puesto por defecto a 1 */
    #[ORM\Column]
    private int $orden = 1;

    /* URL del archivo si el recurso es un documento, imagen o video */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $archivoUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurso(): ?Curso
    {
        return $this->curso;
    }

    public function setCurso(Curso $curso): static
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;
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

    public function getOrden(): int
    {
        return $this->orden;
    }

    public function setOrden(int $orden): static
    {
        $this->orden = $orden;
        return $this;
    }

    public function getArchivoUrl(): ?string
    {
        return $this->archivoUrl;
    }

    public function setArchivoUrl(?string $archivoUrl): static
    {
        $this->archivoUrl = $archivoUrl;
        return $this;
    }
}