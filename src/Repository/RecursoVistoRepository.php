<?php

namespace App\Repository;

use App\Entity\RecursoVisto;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para la entidad RecursoVisto que rastrea los recursos que ha visto un usuario y calcula su progreso. */
class RecursoVistoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecursoVisto::class);
    }

    /* Cuenta cuántos recursos de un curso ha visto un usuario específico */
    public function contarRecursosVistosPorCurso(User $usuario, int $cursoId): int
    {
        return (int) $this->createQueryBuilder('rv')
            ->select('COUNT(rv.id)')
            ->join('rv.recurso', 'r')
            ->where('rv.usuario = :usuario')
            ->andWhere('r.curso = :curso')
            ->setParameter('usuario', $usuario)
            ->setParameter('curso', $cursoId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* Verifica si un usuario ya ha visto un recurso específico */
    public function yaVisto(User $usuario, int $recursoId): bool
    {
        return $this->createQueryBuilder('rv')
            ->join('rv.recurso', 'r')
            ->where('rv.usuario = :usuario')
            ->andWhere('r.id = :recursoId')
            ->setParameter('usuario', $usuario)
            ->setParameter('recursoId', $recursoId)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    /* Busca los IDs de todos los recursos de un curso que ya ha visto un usuario */
    public function buscarIdsRecursosVistosPorCurso(User $usuario, int $cursoId): array
    {
        $filas = $this->createQueryBuilder('rv')
            ->select('IDENTITY(rv.recurso) AS recursoId')
            ->join('rv.recurso', 'r')
            ->where('rv.usuario = :usuario')
            ->andWhere('r.curso = :curso')
            ->setParameter('usuario', $usuario)
            ->setParameter('curso', $cursoId)
            ->getQuery()
            ->getArrayResult();

        return array_map(static fn (array $fila): int => (int) $fila['recursoId'], $filas);
    }
}