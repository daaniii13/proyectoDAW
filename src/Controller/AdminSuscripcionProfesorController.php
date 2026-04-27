<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\SuscripcionProfesor;
use App\Repository\SuscripcionProfesorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de administración de suscripciones de profesores
// Gestiona las solicitudes de alta, cambio de plan y cancelación que los usuarios
// envían para obtener o modificar su rol docente en la plataforma
class AdminSuscripcionProfesorController extends AbstractController
{
    // Muestra el listado de solicitudes de suscripción pendientes de revisión
    #[Route('/admin/profesores', name: 'app_admin_profesores', methods: ['GET'])]
    public function index(SuscripcionProfesorRepository $suscripcionProfesorRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('paginas/admin/profesores.html.twig', [
            // Recupera únicamente las solicitudes que aún no han sido resueltas
            'solicitudesPendientes' => $suscripcionProfesorRepository->buscarPendientes(),
        ]);
    }

    // Aprueba una solicitud de suscripción de profesor
    #[Route('/admin/profesores/{id}/aprobar', name: 'app_admin_aprobar_profesor', methods: ['POST'])]
    public function aprobar(
        SuscripcionProfesor $suscripcionProfesor,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('aprobar_profesor_' . $suscripcionProfesor->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $tipoSolicitud = $suscripcionProfesor->getTipoSolicitud();
        $profesor = $suscripcionProfesor->getProfesor();

        // Procesa la aprobación de una solicitud de cancelación de suscripción
        if ($tipoSolicitud === 'cancelacion') {
            if ($profesor) {
                // Elimina todos los cursos del profesor ya que pierde el acceso docente
                $cursosProfesor = $entityManager->getRepository(Curso::class)->findBy([
                    'profesor' => $profesor,
                ]);

                foreach ($cursosProfesor as $curso) {
                    $entityManager->remove($curso);
                }

                // Elimina el rol de profesor del usuario manteniendo sus demás roles
                $rolesActuales = $profesor->getRoles();
                $rolesNuevos = array_values(array_filter(
                    $rolesActuales,
                    static fn (string $rol): bool => $rol !== 'ROLE_PROFESOR'
                ));

                // Garantiza que el usuario conserve al menos el rol de estudiante
                if (!in_array('ROLE_ESTUDIANTE', $rolesNuevos, true)) {
                    $rolesNuevos[] = 'ROLE_ESTUDIANTE';
                }

                $profesor->setRoles($rolesNuevos);
            }

            $suscripcionProfesor->setEstado('cancelada');
            $suscripcionProfesor->setLimiteCursos(0);
            $suscripcionProfesor->setPlanSolicitado(null);
            $suscripcionProfesor->setTipoSolicitud(null);
            $suscripcionProfesor->setFechaAprobacion(new \DateTimeImmutable());

            $entityManager->flush();

            $this->addFlash('success', 'Cancelación aprobada. El profesor ha pasado a estudiante y sus cursos han sido eliminados.');

            return $this->redirectToRoute('app_admin_profesores');
        }

        // Procesa la aprobación de un alta nueva o un cambio de plan
        // El admin puede confirmar o cambiar el plan antes de aprobar
        $plan = (string) $request->request->get('plan');
        if (!in_array($plan, ['basico', 'pro', 'premium'], true)) {
            $plan = $suscripcionProfesor->getPlanSolicitado() ?: $suscripcionProfesor->getPlan();
        }

        // Calcula el límite de cursos según el plan aprobado
        $limite = match ($plan) {
            'basico' => 1,
            'pro' => 5,
            'premium' => 999,
            default => 1,
        };

        $suscripcionProfesor->setPlan($plan);
        $suscripcionProfesor->setEstado('activa');
        $suscripcionProfesor->setLimiteCursos($limite);
        $suscripcionProfesor->setPlanSolicitado(null);
        $suscripcionProfesor->setTipoSolicitud(null);
        $suscripcionProfesor->setFechaAprobacion(new \DateTimeImmutable());

        // Asigna el rol de profesor al usuario si aún no lo tiene
        if ($profesor) {
            $rolesActuales = $profesor->getRoles();

            if (!in_array('ROLE_PROFESOR', $rolesActuales, true)) {
                $rolesActuales[] = 'ROLE_PROFESOR';
            }

            if (!in_array('ROLE_ESTUDIANTE', $rolesActuales, true)) {
                $rolesActuales[] = 'ROLE_ESTUDIANTE';
            }

            $profesor->setRoles($rolesActuales);
            $profesor->setActivo(true);
        }

        $entityManager->flush();

        $mensaje = $tipoSolicitud === 'cambio_plan'
            ? 'Cambio de plan aprobado correctamente.'
            : 'Profesor aprobado correctamente.';

        $this->addFlash('success', $mensaje);

        return $this->redirectToRoute('app_admin_profesores');
    }

    // Rechaza una solicitud de suscripción de profesor
    #[Route('/admin/profesores/{id}/rechazar', name: 'app_admin_rechazar_profesor', methods: ['POST'])]
    public function rechazar(
        SuscripcionProfesor $suscripcionProfesor,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('rechazar_profesor_' . $suscripcionProfesor->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $tipoSolicitud = $suscripcionProfesor->getTipoSolicitud();

        // El profesor quiso darse de baja pero el admin lo rechaza: La suscripción sigue activa
        if ($tipoSolicitud === 'cancelacion') {
            $suscripcionProfesor->setEstado('activa');
            $suscripcionProfesor->setPlanSolicitado(null);
            $suscripcionProfesor->setTipoSolicitud(null);

            $entityManager->flush();

            $this->addFlash('success', 'Solicitud de cancelación rechazada.');

            return $this->redirectToRoute('app_admin_profesores');
        }

        // El profesor solicitó cambiar de plan pero el admin lo rechaza: Se mantiene el plan actual
        if ($tipoSolicitud === 'cambio_plan') {
            $suscripcionProfesor->setPlanSolicitado(null);
            $suscripcionProfesor->setTipoSolicitud(null);

            $entityManager->flush();

            $this->addFlash('success', 'Solicitud de cambio de plan rechazada.');

            return $this->redirectToRoute('app_admin_profesores');
        }

        // Rechazo de una solicitud de alta: La suscripción queda en estado 'rechazada'
        $suscripcionProfesor->setEstado('rechazada');
        $suscripcionProfesor->setPlanSolicitado(null);
        $suscripcionProfesor->setTipoSolicitud(null);
        $suscripcionProfesor->setFechaAprobacion(new \DateTimeImmutable());

        $entityManager->flush();

        $this->addFlash('success', 'Solicitud rechazada.');

        return $this->redirectToRoute('app_admin_profesores');
    }
}