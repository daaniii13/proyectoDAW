<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\EntregaTareaRepository;
use App\Repository\InscripcionRepository;
use App\Repository\RecursoCursoRepository;
use App\Repository\RecursoVistoRepository;
use App\Repository\TareaCursoRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProgresoCursoService
{
    public function __construct(
        private readonly RecursoCursoRepository $recursoCursoRepository,
        private readonly TareaCursoRepository $tareaCursoRepository,
        private readonly RecursoVistoRepository $recursoVistoRepository,
        private readonly EntregaTareaRepository $entregaTareaRepository,
        private readonly InscripcionRepository $inscripcionRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function recalcular(int $cursoId, User $usuario): void
    {
        $inscripcion = $this->inscripcionRepository
            ->buscarInscripcionPorCursoYEstudiante($cursoId, $usuario);

        if (!$inscripcion) return;

        $totalRecursos = $this->recursoCursoRepository->contarPorCurso($cursoId);
        $totalTareas = $this->tareaCursoRepository->contarPorCurso($cursoId);

        $vistos = $this->recursoVistoRepository
            ->contarRecursosVistosPorCurso($usuario, $cursoId);

        $aprobadas = $this->entregaTareaRepository
            ->contarEntregasAprobadasDeEstudiantePorCurso($usuario, $cursoId);

        $total = $totalRecursos + $totalTareas;
        $hecho = $vistos + $aprobadas;

        $progreso = $total > 0 ? (int) round(($hecho / $total) * 100) : 0;

        if ($progreso > 100) $progreso = 100;

        $inscripcion->setProgreso($progreso);

        if ($progreso >= 100 && $total > 0) {
            $inscripcion->setEstado('completada');
        } else {
            $inscripcion->setEstado('activa');
        }

        $this->entityManager->flush();
    }

    public function recalcularParaTodos(int $cursoId): void
    {
        $inscripciones = $this->inscripcionRepository->buscarPorCurso($cursoId);

        foreach ($inscripciones as $inscripcion) {
            $usuario = $inscripcion->getEstudiante();
            if ($usuario) {
                $this->recalcular($cursoId, $usuario);
            }
        }
    }
}