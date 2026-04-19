<?php

namespace App\Entity;

use App\Repository\RecuperacionContrasenaRepository;
use Doctrine\ORM\Mapping as ORM;

/* Entidad que maneja los tokens de recuperación de contraseña */
#[ORM\Entity(repositoryClass: RecuperacionContrasenaRepository::class)]
class RecuperacionContrasena
{
    /* Clave primaria, su valor lo genera la base de datos automáticamente */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Usuario que solicita la recuperación y que no puede ser nulo */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $usuario = null;

    /* Token único y seguro para recuperar la contraseña, debe ser único en la tabla */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $token = null;

    /* Fecha de expiración del token, se establece automáticamente en la construcción */
    #[ORM\Column]
    private \DateTimeImmutable $fechaExpiracion;

    /* Indica si el token ya ha sido utilizado para evitar reutilizaciones */
    #[ORM\Column]
    private bool $usado = false;

    public function __construct()
    {
        $this->fechaExpiracion = new \DateTimeImmutable('+1 hour');
        $this->usado = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(User $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getFechaExpiracion(): \DateTimeImmutable
    {
        return $this->fechaExpiracion;
    }

    public function setFechaExpiracion(\DateTimeImmutable $fechaExpiracion): static
    {
        $this->fechaExpiracion = $fechaExpiracion;
        return $this;
    }

    public function isUsado(): bool
    {
        return $this->usado;
    }

    public function setUsado(bool $usado): static
    {
        $this->usado = $usado;
        return $this;
    }
}