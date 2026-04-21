<?php

namespace App\Repository;

use App\Entity\TareaCurso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para la entidad TareaCurso que busca tareas de un curso específico y cuenta las ya asignadas. */
class TareaCursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TareaCurso::class);
    }

    /* Busca todas las tareas de un curso ordenadas por orden de creación */
    public function buscarPorCurso(int $cursoId): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.curso', 'c')
            ->addSelect('c')
            ->where('c.id = :cursoId')
            ->setParameter('cursoId', $cursoId)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /* Cuenta el número total de tareas asignadas en un curso */
    public function contarPorCurso(int $cursoId): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->join('t.curso', 'c')
            ->where('c.id = :cursoId')
            ->setParameter('cursoId', $cursoId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}