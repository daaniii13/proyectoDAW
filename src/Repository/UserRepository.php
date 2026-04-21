<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/* Repositorio para la entidad User que va a permitir actualizar contraseñas automáticamente cuando sea necesario. */

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /* Actualiza la contraseña de un usuario con un nuevo hash. */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            // Lanza una excepción si el tipo de usuario no es válido o soportado
            throw new UnsupportedUserException('Las instancias de "' . $user::class . '" no están soportadas.');
        }

        // Se establece la nueva contraseña
        $user->setPassword($newHashedPassword);

        // Se guarda el cambio en la base de datos
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}

