<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\SuscripcionProfesor;
use App\Entity\User;
use App\Repository\CursoRepository;
use App\Repository\InscripcionRepository;
use App\Repository\SuscripcionProfesorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador del panel principal del profesor
// Centraliza la gestión docente: Solicitudes de alta y cambio de plan, cancelación de suscripción,
// creación de cursos y visualización del estado de inscripciones y alumnos por curso
class ProfesorPanelController extends AbstractController
{
    // Página principal del panel del profesor (/profesor/subir-recursos)
    #[Route('/profesor/subir-recursos', name: 'app_subir_recursos', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        CursoRepository $cursoRepository,
        InscripcionRepository $inscripcionRepository,
        SuscripcionProfesorRepository $suscripcionProfesorRepository
    ): Response {
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_PROFESOR') &&
            !$this->isGranted('ROLE_ESTUDIANTE')
        ) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $suscripcionProfesor = $suscripcionProfesorRepository->buscarDeProfesor($usuario);
        // El modo solicitud identifica a estudiantes que aún no tienen rol docente
        $modoSolicitudProfesor = !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROFESOR') && $this->isGranted('ROLE_ESTUDIANTE');

        // Procesa la solicitud de alta desde el panel (solo para estudiantes en modo solicitud)
        if ($request->isMethod('POST') && $request->request->get('formulario') === 'solicitar_alta_profesor_desde_panel') {
            if (!$modoSolicitudProfesor) {
                throw $this->createAccessDeniedException('No puedes realizar esta acción.');
            }

            if (!$this->isCsrfTokenValid('solicitar_alta_profesor_desde_panel', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF no válido.');
            }

            $plan = trim((string) $request->request->get('plan'));
            $confirmacionPago = $request->request->getBoolean('confirmacion_pago');

            if (!in_array($plan, ['basico', 'pro', 'premium'], true)) {
                $this->addFlash('error', 'Debes seleccionar un plan válido.');
                return $this->redirectToRoute('app_subir_recursos');
            }

            if (!$confirmacionPago) {
                $this->addFlash('error', 'Debes confirmar que has revisado la cuenta bancaria y realizado el pago.');
                return $this->redirectToRoute('app_subir_recursos');
            }

            $limite = match ($plan) {
                'basico' => 1,
                'pro' => 5,
                'premium' => 999,
                default => 1,
            };

            if (!$suscripcionProfesor) {
                $suscripcionProfesor = new SuscripcionProfesor();
                $suscripcionProfesor->setProfesor($usuario);
                $entityManager->persist($suscripcionProfesor);
            }

            $suscripcionProfesor->setPlan($plan);
            $suscripcionProfesor->setPlanSolicitado($plan);
            $suscripcionProfesor->setTipoSolicitud('alta');
            $suscripcionProfesor->setEstado('pendiente');
            $suscripcionProfesor->setLimiteCursos($limite);
            $suscripcionProfesor->setFechaSolicitud(new \DateTimeImmutable());

            $entityManager->flush();

            $this->addFlash('success', 'Solicitud docente enviada. El administrador la revisará tras verificar el pago.');

            return $this->redirectToRoute('app_subir_recursos');
        }

        $puedeCrearCursos = true;
        $mensajeSuscripcion = null;

        // Bloque exclusivo para profesores (no admin): Gestión de plan y comprobación de límites
        if ($this->isGranted('ROLE_PROFESOR') && !$this->isGranted('ROLE_ADMIN')) {

            // Si el profesor aún no tiene suscripción, se le crea una pendiente por defecto
            if (!$suscripcionProfesor) {
                $suscripcionProfesor = new SuscripcionProfesor();
                $suscripcionProfesor->setProfesor($usuario);
                $suscripcionProfesor->setPlan('basico');
                $suscripcionProfesor->setPlanSolicitado('basico');
                $suscripcionProfesor->setTipoSolicitud('alta');
                $suscripcionProfesor->setEstado('pendiente');
                $suscripcionProfesor->setLimiteCursos(1);

                $entityManager->persist($suscripcionProfesor);
                $entityManager->flush();
            }

            // Formulario de elección de plan inicial por parte del profesor
            if ($request->isMethod('POST') && $request->request->get('formulario') === 'elegir_plan') {
                if (!$this->isCsrfTokenValid('elegir_plan', (string) $request->request->get('_token'))) {
                    throw $this->createAccessDeniedException('Token CSRF no válido.');
                }

                $plan = trim((string) $request->request->get('plan'));
                $confirmacionPago = $request->request->getBoolean('confirmacion_pago');

                if (!in_array($plan, ['basico', 'pro', 'premium'], true)) {
                    $this->addFlash('error', 'Debes seleccionar un plan válido.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                if (!$confirmacionPago) {
                    $this->addFlash('error', 'Debes confirmar que has revisado la cuenta bancaria y realizado el pago.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                $limite = match ($plan) {
                    'basico' => 1,
                    'pro' => 5,
                    'premium' => 999,
                    default => 1,
                };

                $suscripcionProfesor->setPlan($plan);
                $suscripcionProfesor->setPlanSolicitado($plan);
                $suscripcionProfesor->setTipoSolicitud('alta');
                $suscripcionProfesor->setEstado('pendiente');
                $suscripcionProfesor->setLimiteCursos($limite);
                $suscripcionProfesor->setFechaSolicitud(new \DateTimeImmutable());

                $entityManager->flush();

                $this->addFlash('success', 'Pago confirmado. La solicitud de alta docente queda pendiente de aprobación por el administrador.');

                return $this->redirectToRoute('app_subir_recursos');
            }

            // Formulario de solicitud de cambio de plan
            if ($request->isMethod('POST') && $request->request->get('formulario') === 'solicitar_cambio_plan') {
                if (!$this->isCsrfTokenValid('solicitar_cambio_plan', (string) $request->request->get('_token'))) {
                    throw $this->createAccessDeniedException('Token CSRF no válido.');
                }

                $plan = trim((string) $request->request->get('plan'));
                $confirmacionPago = $request->request->getBoolean('confirmacion_pago');

                if (!in_array($plan, ['basico', 'pro', 'premium'], true)) {
                    $this->addFlash('error', 'Debes seleccionar un plan válido.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                if (!$confirmacionPago) {
                    $this->addFlash('error', 'Debes confirmar que has revisado la cuenta bancaria y realizado el pago.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                if ($suscripcionProfesor->getEstado() !== 'activa') {
                    $this->addFlash('error', 'Solo puedes solicitar un cambio de plan si tu suscripción está activa.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                if ($suscripcionProfesor->getTipoSolicitud() !== null) {
                    $this->addFlash('error', 'Ya tienes una solicitud pendiente de revisión.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                if ($plan === $suscripcionProfesor->getPlan()) {
                    $this->addFlash('error', 'Ya tienes ese plan activo.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                $suscripcionProfesor->setPlanSolicitado($plan);
                $suscripcionProfesor->setTipoSolicitud('cambio_plan');
                $suscripcionProfesor->setFechaSolicitud(new \DateTimeImmutable());

                $entityManager->flush();

                $this->addFlash('success', 'Pago confirmado. La solicitud de cambio de plan ha sido enviada al administrador.');

                return $this->redirectToRoute('app_subir_recursos');
            }

            // Formulario de solicitud de cancelación de suscripción
            if ($request->isMethod('POST') && $request->request->get('formulario') === 'solicitar_cancelacion') {
                if (!$this->isCsrfTokenValid('solicitar_cancelacion', (string) $request->request->get('_token'))) {
                    throw $this->createAccessDeniedException('Token CSRF no válido.');
                }

                if ($suscripcionProfesor->getEstado() !== 'activa') {
                    $this->addFlash('error', 'Solo puedes cancelar una suscripción activa.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                if ($suscripcionProfesor->getTipoSolicitud() !== null) {
                    $this->addFlash('error', 'Ya tienes una solicitud pendiente de revisión.');
                    return $this->redirectToRoute('app_subir_recursos');
                }

                $suscripcionProfesor->setTipoSolicitud('cancelacion');
                $suscripcionProfesor->setFechaSolicitud(new \DateTimeImmutable());

                $entityManager->flush();

                $this->addFlash('success', 'Solicitud de cancelación enviada al administrador.');

                return $this->redirectToRoute('app_subir_recursos');
            }

            // Determina si el profesor puede crear más cursos según su suscripción y límite de plan
            $cursosProfesorActuales = $cursoRepository->contarCursosDeProfesor($usuario);

            if ($suscripcionProfesor->getEstado() !== 'activa') {
                $puedeCrearCursos = false;
                $mensajeSuscripcion = 'Tu perfil de profesor todavía no ha sido aprobado por el administrador.';
            } elseif ($cursosProfesorActuales >= $suscripcionProfesor->getLimiteCursos()) {
                $puedeCrearCursos = false;
                $mensajeSuscripcion = 'Has alcanzado el límite de cursos permitido por tu plan actual.';
            }

            // Actualiza el mensaje informativo según el tipo de solicitud pendiente
            if ($suscripcionProfesor->getTipoSolicitud() === 'cambio_plan') {
                $mensajeSuscripcion = 'Tienes una solicitud de cambio de plan pendiente de aprobación.';
            } elseif ($suscripcionProfesor->getTipoSolicitud() === 'cancelacion') {
                $mensajeSuscripcion = 'Tienes una solicitud de cancelación pendiente de aprobación.';
            } elseif ($suscripcionProfesor->getTipoSolicitud() === 'alta' && $suscripcionProfesor->getEstado() === 'pendiente') {
                $mensajeSuscripcion = 'Tu perfil de profesor todavía no ha sido aprobado por el administrador.';
            }
        }

        // Formulario de creación de curso
        $errorCurso = null;
        $datosCurso = [
            'titulo' => '',
            'descripcion' => '',
            'nivel' => '',
            'modalidad' => '',
            'duracion' => '',
            'precio' => '',
            'banner_url' => '',
            'mapa_url' => '',
        ];

        if ($request->isMethod('POST') && $request->request->get('formulario') === 'crear_curso') {
            if (!$this->isCsrfTokenValid('crear_curso', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF no válido.');
            }

            if (!$puedeCrearCursos) {
                $this->addFlash('error', $mensajeSuscripcion ?? 'No puedes crear cursos en este momento.');
                return $this->redirectToRoute('app_subir_recursos');
            }

            $titulo = trim((string) $request->request->get('titulo_curso'));
            $descripcion = trim((string) $request->request->get('descripcion_curso'));
            $nivel = trim((string) $request->request->get('nivel_curso'));
            $modalidad = trim((string) $request->request->get('modalidad_curso'));
            $duracion = trim((string) $request->request->get('duracion_curso'));
            $precio = trim((string) $request->request->get('precio_curso'));
            $bannerUrl = trim((string) $request->request->get('banner_url'));
            $mapaUrl = trim((string) $request->request->get('mapa_url'));

            $datosCurso = [
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'nivel' => $nivel,
                'modalidad' => $modalidad,
                'duracion' => $duracion,
                'precio' => $precio,
                'banner_url' => $bannerUrl,
                'mapa_url' => $mapaUrl,
            ];

            if (
                $titulo === '' || $descripcion === '' || $nivel === '' ||
                $modalidad === '' || $duracion === '' || $precio === ''
            ) {
                $errorCurso = 'Todos los campos obligatorios del curso deben completarse.';
            } elseif (!is_numeric($precio) || (float) $precio < 0) {
                $errorCurso = 'El precio debe ser un número válido igual o mayor que 0.';
            } else {
                $curso = new Curso();
                $curso->setTitulo($titulo);
                $curso->setDescripcion($descripcion);
                $curso->setNivel($nivel);
                $curso->setModalidad($modalidad);
                $curso->setDuracion($duracion);
                $curso->setPrecio(number_format((float) $precio, 2, '.', ''));
                $curso->setEstado('activo');
                $curso->setProfesor($usuario);
                $curso->setBannerUrl($bannerUrl !== '' ? $bannerUrl : null);
                $curso->setMapaUrl($mapaUrl !== '' ? $mapaUrl : null);

                $entityManager->persist($curso);
                $entityManager->flush();

                $this->addFlash('success', 'Curso creado correctamente.');

                return $this->redirectToRoute('app_subir_recursos');
            }
        }

        $cursosProfesor = [];
        $solicitudesPendientes = [];
        $alumnosPorCurso = [];

        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_PROFESOR')) {
            $cursosProfesor = $cursoRepository->buscarCursosDeProfesor($usuario);
            $solicitudesPendientes = $inscripcionRepository->buscarSolicitudesPendientesDeProfesor($usuario);

            foreach ($cursosProfesor as $cursoProfesor) {
                $alumnosPorCurso[$cursoProfesor->getId()] = $inscripcionRepository->buscarAlumnosPorCurso($cursoProfesor->getId());
            }
        }

        return $this->render('paginas/profesor/subir_recursos.html.twig', [
            'errorCurso' => $errorCurso,
            'datosCurso' => $datosCurso,
            'cursosProfesor' => $cursosProfesor,
            'solicitudesPendientes' => $solicitudesPendientes,
            'suscripcionProfesor' => $suscripcionProfesor,
            'puedeCrearCursos' => $puedeCrearCursos,
            'mensajeSuscripcion' => $mensajeSuscripcion,
            'alumnosPorCurso' => $alumnosPorCurso,
            'modoSolicitudProfesor' => $modoSolicitudProfesor,
        ]);
    }
}
