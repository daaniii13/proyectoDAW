<?php

namespace App\Service;
use App\Entity\User;
use App\Repository\EntregaTareaRepository;
use App\Repository\InscripcionRepository;
use App\Repository\RecursoCursoRepository;
use App\Repository\RecursoVistoRepository;
use App\Repository\TareaCursoRepository;
use Doctrine\ORM\EntityManagerInterface;

// Servicio encargado de recalcular el progreso de un estudiante en un curso
class ProgresoCursoService {
    public function __construct(
        private readonly RecursoCursoRepository $recursoCursoRepository,
        private readonly TareaCursoRepository $tareaCursoRepository,
        private readonly RecursoVistoRepository $recursoVistoRepository,
        private readonly EntregaTareaRepository $entregaTareaRepository,
        private readonly InscripcionRepository $inscripcionRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    // Recalcula el progreso de un usuario concreto en un curso
    public function recalcular(int $cursoId, User $usuario): void {
        // Busca la inscripción del estudiante en ese curso
        $inscripcion = $this->inscripcionRepository
            ->buscarInscripcionPorCursoYEstudiante($cursoId, $usuario);

        if (!$inscripcion) return;

        // Cuenta recursos y tareas totales del curso
        $totalRecursos = $this->recursoCursoRepository->contarPorCurso($cursoId);
        $totalTareas = $this->tareaCursoRepository->contarPorCurso($cursoId);

        // Cuenta recursos vistos y tareas aprobadas por el estudiante
        $vistos = $this->recursoVistoRepository
            ->contarRecursosVistosPorCurso($usuario, $cursoId);

        $aprobadas = $this->entregaTareaRepository
            ->contarEntregasAprobadasDeEstudiantePorCurso($usuario, $cursoId);

        // Calcula el porcentaje de progreso
        $total = $totalRecursos + $totalTareas;
        $hecho = $vistos + $aprobadas;

        $progreso = $total > 0 ? (int) round(($hecho / $total) * 100) : 0;

        // Evita que el progreso pase del 100%
        if ($progreso > 100) $progreso = 100;

        $inscripcion->setProgreso($progreso);

        // Si llega al 100%, marca el curso como completado
        if ($progreso >= 100 && $total > 0) {
            $inscripcion->setEstado('completada');
        } else {
            $inscripcion->setEstado('activa');
        }

        // Guarda los cambios en base de datos
        $this->entityManager->flush();
    }

    // Recalcula el progreso de todos los estudiantes inscritos en un curso
    public function recalcularParaTodos(int $cursoId): void  {
        $inscripciones = $this->inscripcionRepository->buscarPorCurso($cursoId);

        foreach ($inscripciones as $inscripcion) {
            $usuario = $inscripcion->getEstudiante();

            if ($usuario) {
                $this->recalcular($cursoId, $usuario);
            }
        }
    }
}
