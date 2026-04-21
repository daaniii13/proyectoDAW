<?php

namespace App\Repository;

use App\Entity\EntregaComentario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para la entidad EntregaComentario para las consultas específicas de comentarios en entregas de tareas. */
class EntregaComentarioRepository extends ServiceEntityRepository{

    /* Constructor que vincula este repositorio con la entidad EntregaComentario */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntregaComentario::class);
    }

    /* Busca todos los comentarios de una entrega específica ordenados cronológicamente */
    public function buscarPorEntrega(int $entregaId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.entrega', 'e')
            ->where('e.id = :id')
            ->setParameter('id', $entregaId)
            ->orderBy('c.fecha', 'ASC')
            ->getQuery()
            ->getResult();
    }
}