<?php

namespace App\Repository;

use App\Entity\EntregaTarea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para gestionar consultas relacionadas con las entregas de tareas */
class EntregaTareaRepository extends ServiceEntityRepository
{
    /* Vincula este repositorio con la entidad EntregaTarea */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntregaTarea::class);
    }

    /* Obtener una única entrega de un estudiante para una tarea concreta */
    public function buscarUnaEntrega(int $tareaId, User $estudiante): ?EntregaTarea
    {
        return $this->createQueryBuilder('e')
            ->join('e.tarea', 't')
            ->where('t.id = :tareaId')
            ->andWhere('e.estudiante = :estudiante')
            ->setParameter('tareaId', $tareaId)
            ->setParameter('estudiante', $estudiante)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /* Obtener todas las entregas de un estudiante dentro de un curso */
    public function buscarEntregasDeEstudiantePorCurso(User $estudiante, int $cursoId): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.tarea', 't')
            ->addSelect('t')
            ->join('t.curso', 'c')
            ->where('e.estudiante = :estudiante')
            ->andWhere('c.id = :cursoId')
            ->setParameter('estudiante', $estudiante)
            ->setParameter('cursoId', $cursoId)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /* Contar cuántas entregas han sido aprobadas en un curso por un estudiante */
    public function contarEntregasAprobadasDeEstudiantePorCurso(User $estudiante, int $cursoId): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)') // Conteo
            ->join('e.tarea', 't')
            ->join('t.curso', 'c')
            ->where('e.estudiante = :estudiante')
            ->andWhere('c.id = :cursoId')
            ->andWhere('e.estadoRevision = :estado') // Solo aprobadas
            ->setParameter('estudiante', $estudiante)
            ->setParameter('cursoId', $cursoId)
            ->setParameter('estado', 'aprobada')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* Obtener todas las entregas de un curso (vista profesor) */
    public function buscarEntregasPorCurso(int $cursoId): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.tarea', 't')
            ->addSelect('t')
            ->join('t.curso', 'c')
            ->addSelect('c')
            ->join('e.estudiante', 'u')
            ->addSelect('u') // Incluye estudiante para mostrar quién entregó
            ->where('c.id = :cursoId')
            ->setParameter('cursoId', $cursoId)
            ->orderBy('e.estadoRevision', 'ASC')
            ->addOrderBy('e.fechaEntrega', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Obtener solo las entregas pendientes de revisión de un curso */
    public function buscarEntregasPendientesPorCurso(int $cursoId): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.tarea', 't')
            ->addSelect('t')
            ->join('t.curso', 'c')
            ->addSelect('c')
            ->join('e.estudiante', 'u')
            ->addSelect('u')
            ->where('c.id = :cursoId')
            ->andWhere('e.estadoRevision = :estado')
            ->setParameter('cursoId', $cursoId)
            ->setParameter('estado', 'pendiente')
            ->orderBy('e.fechaEntrega', 'DESC') 
            ->getQuery()
            ->getResult();
    }
}