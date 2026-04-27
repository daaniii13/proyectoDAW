<?php

namespace App\Repository;

use App\Entity\RecuperacionContrasena;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para la entidad RecuperacionContrasena y su objetivo es proporcionar métodos para
   buscar y validar tokens de recuperación de contraseña. */
class RecuperacionContrasenaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecuperacionContrasena::class);
    }

    /* Busca un token válido verificando que no haya expirado y no haya sido usado */
    public function buscarTokenValido(string $token): ?RecuperacionContrasena
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.token = :token')
            ->andWhere('r.usado = false')
            ->andWhere('r.fechaExpiracion > :ahora')
            ->setParameter('token', $token)
            ->setParameter('ahora', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }
}