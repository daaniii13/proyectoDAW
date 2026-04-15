<?php

namespace App\Repository;

use App\Entity\EntregaTarea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// Repositorio para gestionar consultas relacionadas con las entregas de tareas
class EntregaTareaRepository extends ServiceEntityRepository
{
    // Constructor que vincula el repositorio con la entidad EntregaTarea
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntregaTarea::class);
    }

    // Buscar una única entrega de un estudiante para una tarea concreta
    public function buscarUnaEntrega(int $tareaId, User $estudiante): ?EntregaTarea
    {
        return $this->createQueryBuilder('e')
            ->join('e.tarea', 't') // Relacionamos con la tarea
            ->where('t.id = :tareaId') // Filtramos por ID de la tarea
            ->andWhere('e.estudiante = :estudiante') // Filtramos por estudiante
            ->setParameter('tareaId', $tareaId)
            ->setParameter('estudiante', $estudiante)
            ->getQuery()
            ->getOneOrNullResult(); // Devuelve una entrega o null si no existe
    }

    // Obtener todas las entregas de un estudiante en un curso concreto
    public function buscarEntregasDeEstudiantePorCurso(User $estudiante, int $cursoId): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.tarea', 't')
            ->addSelect('t') // Incluimos la tarea para evitar consultas adicionales
            ->join('t.curso', 'c') // Relacionamos la tarea con su curso
            ->where('e.estudiante = :estudiante')
            ->andWhere('c.id = :cursoId') // Filtramos por curso
            ->setParameter('estudiante', $estudiante)
            ->setParameter('cursoId', $cursoId)
            ->getQuery()
            ->getResult();
    }

    // Contar cuántas entregas ha hecho un estudiante en un curso
    public function contarEntregasDeEstudiantePorCurso(User $estudiante, int $cursoId): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)') // Conteo de entregas
            ->join('e.tarea', 't')
            ->join('t.curso', 'c')
            ->where('e.estudiante = :estudiante')
            ->andWhere('c.id = :cursoId')
            ->setParameter('estudiante', $estudiante)
            ->setParameter('cursoId', $cursoId)
            ->getQuery()
            ->getSingleScalarResult(); // Devuelve un único número
    }
}