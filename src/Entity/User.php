<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/* Entidad que representa los usuarios del sistema, ya sean estudiante o profesor. */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* Email del usuario, debe ser único para identificación y login */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /* Array de roles del usuario (estudiante, profesor o admin) */
    #[ORM\Column]
    private array $roles = [];

    /* Contraseña del usuario para autenticación segura */
    #[ORM\Column]
    private ?string $password = null;

    /* Nombre del usuario para mostrar en la aplicación */
    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    /* Fecha de registro del usuario */
    #[ORM\Column]
    private \DateTimeImmutable $fechaRegistro;

    /* Indica si el usuario está activo */
    #[ORM\Column]
    private bool $activo = true;

    /* URL de la foto de perfil del usuario */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fotoPerfil = null;

    public function __construct()
    {
        $this->roles = ['ROLE_ESTUDIANTE'];
        $this->fechaRegistro = new \DateTimeImmutable();
        $this->activo = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return array_values(array_unique($this->roles));
    }

    public function setRoles(array $roles): static
    {
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getFechaRegistro(): \DateTimeImmutable
    {
        return $this->fechaRegistro;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;
        return $this;
    }

    public function getFotoPerfil(): ?string
    {
        return $this->fotoPerfil;
    }

    public function setFotoPerfil(?string $fotoPerfil): static
    {
        $this->fotoPerfil = $fotoPerfil;
        return $this;
    }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);
        return $data;
    }

    public function eraseCredentials(): void
    {
    }
}