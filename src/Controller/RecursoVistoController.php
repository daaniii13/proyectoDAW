<?php

namespace App\Controller;

use App\Entity\RecursoVisto;
use App\Entity\User;
use App\Repository\InscripcionRepository;
use App\Repository\RecursoCursoRepository;
use App\Repository\RecursoVistoRepository;
use App\Service\ProgresoCursoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador para marcar recursos como vistos por el alumno
class RecursoVistoController extends AbstractController
{
    // Registra un recurso como visto por el alumno
    // Si el recurso ya estaba marcado como visto no crea un registro duplicado
    #[Route('/recurso/visto/{id}', name: 'app_recurso_visto', methods: ['POST'])]
    public function marcarVisto(
        int $id,
        Request $request,
        RecursoCursoRepository $recursoRepository,
        RecursoVistoRepository $recursoVistoRepository,
        InscripcionRepository $inscripcionRepository,
        EntityManagerInterface $entityManager,
        ProgresoCursoService $progresoCursoService
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $recurso = $recursoRepository->find($id);

        if (!$recurso) {
            throw $this->createNotFoundException('Recurso no encontrado.');
        }

        if (!$this->isCsrfTokenValid('marcar_recurso_visto_' . $recurso->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $curso = $recurso->getCurso();

        if (!$curso) {
            throw $this->createNotFoundException('El recurso no está asociado a ningún curso.');
        }

        $inscripcion = $inscripcionRepository->buscarInscripcionPorCursoYEstudiante($curso->getId(), $usuario);

        if (!$inscripcion || !in_array($inscripcion->getEstado(), ['activa', 'completada'], true)) {
            throw $this->createAccessDeniedException('No tienes acceso a este recurso.');
        }

        // Verifica que todos los recursos con orden anterior hayan sido completados
        $recursosAnteriores = $recursoRepository->buscarRecursosAnteriores($curso->getId(), $recurso->getOrden());

        foreach ($recursosAnteriores as $recursoAnterior) {
            if (!$recursoVistoRepository->yaVisto($usuario, $recursoAnterior->getId())) {
                $this->addFlash('error', 'Debes completar primero los recursos anteriores.');
                return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
            }
        }

        // Solo crea el registro si el recurso no había sido marcado antes
        if (!$recursoVistoRepository->yaVisto($usuario, $recurso->getId())) {
            $recursoVisto = new RecursoVisto();
            $recursoVisto->setUsuario($usuario);
            $recursoVisto->setRecurso($recurso);
            $recursoVisto->setFechaVisto(new \DateTimeImmutable());

            $entityManager->persist($recursoVisto);
            $entityManager->flush();
        }

        // Recalcula el progreso individual
        $progresoCursoService->recalcular($curso->getId(), $usuario);

        return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
    }
}