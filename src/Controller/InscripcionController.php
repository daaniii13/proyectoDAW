<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\Inscripcion;
use App\Entity\User;
use App\Repository\InscripcionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de inscripciones a cursos
// Gestiona el proceso por el que un estudiante o profesor solicita inscribirse en un curso
// La inscripción se crea inicialmente en estado 'pendiente_pago' y el alumno debe
// realizar el pago manualmente y notificarlo para que el profesor lo valide
class InscripcionController extends AbstractController
{
    // Crea una nueva solicitud de inscripción en el curso indicado
    // Impide la inscripción de administradores, del propio profesor del curso
    // y de usuarios que ya tengan una inscripción existente en el mismo curso
    #[Route('/curso/{id}/inscribirse', name: 'app_inscribirse_curso', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function inscribirse(
        Curso $curso,
        Request $request,
        InscripcionRepository $inscripcionRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            !$this->isGranted('ROLE_ESTUDIANTE') &&
            !$this->isGranted('ROLE_PROFESOR')
        ) {
            throw $this->createAccessDeniedException('No tienes permisos para inscribirte.');
        }

        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Un administrador no puede inscribirse en cursos.');
            return $this->redirectToRoute('app_detalle_curso', ['id' => $curso->getId()]);
        }

        if ($curso->getProfesor()?->getId() === $usuario->getId()) {
            $this->addFlash('error', 'No puedes inscribirte en tu propio curso.');
            return $this->redirectToRoute('app_detalle_curso', ['id' => $curso->getId()]);
        }

        // Solo los cursos activos aceptan nuevas inscripciones
        if ($curso->getEstado() !== 'activo') {
            $this->addFlash('error', 'Este curso no admite nuevas inscripciones en este momento.');
            return $this->redirectToRoute('app_detalle_curso', ['id' => $curso->getId()]);
        }

        if (!$this->isCsrfTokenValid('inscribirse_curso_' . $curso->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $inscripcionExistente = $inscripcionRepository->buscarUnaInscripcion($usuario, $curso->getId());

        if ($inscripcionExistente) {
            $this->addFlash('error', 'Ya existe una solicitud o inscripción para este curso.');
            return $this->redirectToRoute('app_detalle_curso', ['id' => $curso->getId()]);
        }

        $inscripcion = new Inscripcion();
        $inscripcion->setEstudiante($usuario);
        $inscripcion->setCurso($curso);
        $inscripcion->setProgreso(0);
        $inscripcion->setEstado('pendiente_pago');
        // Genera una referencia única para que el profesor identifique el pago del alumno
        $inscripcion->setReferenciaPago('PAGO-CURSO-' . $curso->getId() . '-USER-' . $usuario->getId());

        $entityManager->persist($inscripcion);
        $entityManager->flush();

        $this->addFlash('success', 'Solicitud creada. Primero debes realizar el pago manual al profesor. El contenido se desbloqueará cuando el profesor valide tu pago.');

        return $this->redirectToRoute('app_mis_cursos');
    }
}