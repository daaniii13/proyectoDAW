<?php

namespace App\Repository;

use App\Entity\RecursoCurso;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para la entidad RecursoCurso que sirve para buscar recursos ordenados y contar recursos en cursos específicos. */
class RecursoCursoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecursoCurso::class);
    }

    /* Busca todos los recursos de un curso ordenados por el orden establecido */
    public function buscarPorCurso(int $cursoId): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.curso', 'c')
            ->addSelect('c')
            ->where('c.id = :cursoId')
            ->setParameter('cursoId', $cursoId)
            ->orderBy('r.orden', 'ASC')
            ->addOrderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /* Busca los recursos anteriores a un recurso específico en un curso */
    public function buscarRecursosAnteriores(int $cursoId, int $ordenActual): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.curso', 'c')
            ->where('c.id = :cursoId')
            ->andWhere('r.orden < :ordenActual')
            ->setParameter('cursoId', $cursoId)
            ->setParameter('ordenActual', $ordenActual)
            ->orderBy('r.orden', 'ASC')
            ->addOrderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /* Cuenta el número total de recursos que tiene un curso */
    public function contarPorCurso(int $cursoId): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.curso', 'c')
            ->where('c.id = :cursoId')
            ->setParameter('cursoId', $cursoId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* Cuenta el número total de recursos creados por un profesor en todos sus cursos */
    public function contarRecursosDeProfesor(User $profesor): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.curso', 'c')
            ->where('c.profesor = :profesor')
            ->setParameter('profesor', $profesor)
            ->getQuery()
            ->getSingleScalarResult();
    }
}