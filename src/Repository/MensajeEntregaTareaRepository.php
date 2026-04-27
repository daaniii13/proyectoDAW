<?php

namespace App\Repository;

use App\Entity\EntregaTarea;
use App\Entity\MensajeEntregaTarea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio de mensajes del mini chat por entrega */
class MensajeEntregaTareaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MensajeEntregaTarea::class);
    }

    /* Devuelve los mensajes de una entrega ordenados por fecha ascendente */
    public function buscarPorEntrega(EntregaTarea|int $entrega): array
    {
        $entregaId = $entrega instanceof EntregaTarea ? $entrega->getId() : $entrega;

        return $this->createQueryBuilder('m')
            ->andWhere('m.entrega = :entrega')
            ->setParameter('entrega', $entregaId)
            ->orderBy('m.fechaCreacion', 'ASC')
            ->getQuery()
            ->getResult();
    }
}