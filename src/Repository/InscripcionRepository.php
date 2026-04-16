<?php

namespace App\Repository;

use App\Entity\Inscripcion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/* Repositorio para gestionar inscripciones de estudiantes a cursos */
class InscripcionRepository extends ServiceEntityRepository
{
    /* Vincula este repositorio con la entidad Inscripcion */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscripcion::class);
    }

    /* Obtener todas las inscripciones de un estudiante */
    public function buscarInscripcionesDeEstudiante(User $estudiante): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.curso', 'c')
            ->leftJoin('c.profesor', 'p') // Puede haber cursos sin profesor asignado
            ->addSelect('c', 'p')
            ->where('i.estudiante = :estudiante')
            ->setParameter('estudiante', $estudiante)
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Buscar una inscripción concreta de un estudiante en un curso */

    public function buscarUnaInscripcion(User $estudiante, int $cursoId): ?Inscripcion
    {
        return $this->createQueryBuilder('i')
            ->join('i.curso', 'c')
            ->where('i.estudiante = :estudiante')
            ->andWhere('c.id = :cursoId')
            ->setParameter('estudiante', $estudiante)
            ->setParameter('cursoId', $cursoId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /* Contar cuántos cursos tiene un estudiante */
    public function contarInscripcionesDeEstudiante(User $estudiante): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.estudiante = :estudiante')
            ->setParameter('estudiante', $estudiante)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* Contar cuántos alumnos tiene un profesor en total  */
    public function contarInscripcionesEnCursosDeProfesor(User $profesor): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->join('i.curso', 'c')
            ->where('c.profesor = :profesor')
            ->setParameter('profesor', $profesor)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /* Calcular el progreso medio de un estudiante en todos sus cursos */
    public function calcularProgresoMedioDeEstudiante(User $estudiante): int
    {
        $resultado = $this->createQueryBuilder('i')
            ->select('AVG(i.progreso) as progreso_medio') // Media del progreso
            ->where('i.estudiante = :estudiante')
            ->setParameter('estudiante', $estudiante)
            ->getQuery()
            ->getOneOrNullResult();

        /* Si no hay datos, devolvemos 0 */
        if (!$resultado || $resultado['progreso_medio'] === null) {
            return 0;
        }

        /* Redondeamos el resultado a entero */
        return (int) round((float) $resultado['progreso_medio']);
    }

    /* Obtener solicitudes pendientes de validación para un profesor */
    public function buscarSolicitudesPendientesDeProfesor(User $profesor): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.curso', 'c')
            ->join('i.estudiante', 'e')
            ->addSelect('c', 'e') // Incluye curso y estudiante
            ->where('c.profesor = :profesor')
            ->andWhere('i.estado = :estado') // Solo pendientes de validación
            ->setParameter('profesor', $profesor)
            ->setParameter('estado', 'pendiente_validacion')
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Buscar una inscripción concreta asegurando que pertenece a un profesor */
    public function buscarInscripcionDeProfesorPorId(User $profesor, int $inscripcionId): ?Inscripcion
    {
        return $this->createQueryBuilder('i')
            ->join('i.curso', 'c')
            ->where('i.id = :inscripcionId')
            ->andWhere('c.profesor = :profesor') // Control de acceso
            ->setParameter('inscripcionId', $inscripcionId)
            ->setParameter('profesor', $profesor)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /* Obtener alumnos de un curso con estado válido */
    public function buscarAlumnosPorCurso(int $cursoId): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.estudiante', 'u')
            ->addSelect('u')
            ->join('i.curso', 'c')
            ->addSelect('c')
            ->where('c.id = :cursoId')
            ->andWhere('i.estado IN (:estados)')
            ->setParameter('cursoId', $cursoId)
            ->setParameter('estados', ['activa', 'completada'])
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /* Obtener inscripciones de un curso pero más simple */
    public function buscarPorCurso(int $cursoId): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.estudiante', 'u')
            ->where('i.curso = :cursoId')
            ->andWhere('u IS NOT NULL') // Asegura que hay estudiante asociado
            ->setParameter('cursoId', $cursoId)
            ->getQuery()
            ->getResult();
    }
}