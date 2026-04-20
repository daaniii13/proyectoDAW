<?php

namespace App\Repository;

use App\Entity\SuscripcionProfesor;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para la entidad SuscripcionProfesor para buscar suscripciones de profesores y gestionar solicitudes pendientes. */
class SuscripcionProfesorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuscripcionProfesor::class);
    }

    /* Busca la suscripción de un profesor específico */
    public function buscarProfesor(User $profesor): ?SuscripcionProfesor
    {
        return $this->findOneBy(['profesor' => $profesor]);
    }

    /* Busca todas las suscripciones pendientes de validación o con solicitudes de cambio */
    public function buscarPendientes(): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.profesor', 'p')
            ->addSelect('p')
            ->where('s.estado = :estadoPendiente')
            ->orWhere('s.tipoSolicitud IN (:tiposSolicitud)')
            ->setParameter('estadoPendiente', 'pendiente')
            ->setParameter('tiposSolicitud', ['cambio_plan', 'cancelacion'])
            ->orderBy('s.fechaSolicitud', 'DESC')
            ->getQuery()
            ->getResult();
    }
}