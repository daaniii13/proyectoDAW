<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\User;
use App\Repository\ComentarioRepository;
use App\Repository\CursoRepository;
use App\Repository\EntregaTareaRepository;
use App\Repository\InscripcionRepository;
use App\Repository\RecursoCursoRepository;
use App\Repository\RecursoVistoRepository;
use App\Repository\TareaCursoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de las páginas principales de la aplicación
// Agrupa las rutas de navegación general: Inicio, sobre nosotros, listado de cursos del usuario, detalle de curso y visor de contenido
class PaginasController extends AbstractController
{
    // Página de inicio. Muestra el catálogo de cursos
    // Si el usuario está autenticado, muestra además un panel de estadísticas personalizado según su rol
    #[Route('/', name: 'app_inicio')]
    public function inicio(
        Request $request,
        CursoRepository $cursoRepository,
        InscripcionRepository $inscripcionRepository,
        RecursoCursoRepository $recursoCursoRepository
    ): Response {
        $busqueda = trim((string) $request->query->get('q', ''));
        $nivel = trim((string) $request->query->get('nivel', ''));
        $modalidad = trim((string) $request->query->get('modalidad', ''));

        $cursos = $cursoRepository->buscarCursosActivosFiltrados($busqueda, $nivel, $modalidad);

        $usuarioActual = $this->getUser();
        $datosPanel = null;

        if ($usuarioActual instanceof User) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $datosPanel = [
                    'tipo' => 'admin',
                    'totalCursos' => $cursoRepository->contarCursosDeProfesor($usuarioActual),
                    'cursosActivos' => $cursoRepository->contarCursosActivosDeProfesor($usuarioActual),
                    'totalInscripciones' => $inscripcionRepository->contarInscripcionesDeEstudiante($usuarioActual),
                    'totalRecursos' => $recursoCursoRepository->contarRecursosDeProfesor($usuarioActual),
                ];
            } elseif ($this->isGranted('ROLE_PROFESOR')) {
                $datosPanel = [
                    'tipo' => 'profesor',
                    'totalCursos' => $cursoRepository->contarCursosDeProfesor($usuarioActual),
                    'cursosActivos' => $cursoRepository->contarCursosActivosDeProfesor($usuarioActual),
                    'totalInscripciones' => $inscripcionRepository->contarInscripcionesEnCursosDeProfesor($usuarioActual),
                    'totalRecursos' => $recursoCursoRepository->contarRecursosDeProfesor($usuarioActual),
                ];
            } elseif ($this->isGranted('ROLE_ESTUDIANTE')) {
                $datosPanel = [
                    'tipo' => 'estudiante',
                    'totalInscripciones' => $inscripcionRepository->contarInscripcionesDeEstudiante($usuarioActual),
                    'progresoMedio' => $inscripcionRepository->calcularProgresoMedioDeEstudiante($usuarioActual),
                ];
            }
        }

        return $this->render('paginas/inicio/index.html.twig', [
            'cursos' => $cursos,
            'datosPanel' => $datosPanel,
            'filtros' => [
                'q' => $busqueda,
                'nivel' => $nivel,
                'modalidad' => $modalidad,
            ],
        ]);
    }

    // Muestra la página "Sobre nosotros"
    #[Route('/sobre-nosotros', name: 'app_sobre_nosotros')]
    public function sobreNosotros(): Response
    {
        return $this->render('paginas/sobre_nosotros/index.html.twig');
    }

    // Muestra el listado de cursos en los que el usuario está inscrito
    // Requiere estar autenticado con rol de admin, estudiante o profesor
    #[Route('/mis-cursos', name: 'app_mis_cursos')]
    public function misCursos(InscripcionRepository $inscripcionRepository): Response
    {
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_ESTUDIANTE') &&
            !$this->isGranted('ROLE_PROFESOR')
        ) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        $inscripciones = $inscripcionRepository->buscarInscripcionesDeEstudiante($this->getUser());

        return $this->render('paginas/cursos/mis_cursos.html.twig', [
            'inscripciones' => $inscripciones,
        ]);
    }

    // Muestra la página de detalle de un curso: Descripción, comentarios e información de si el usuario ya está inscrito
    // Registra el ID del curso en el historial de visitas de la sesión, que el recomendador utiliza como señal de interés del usuario
    #[Route('/curso/detalle/{id}', name: 'app_detalle_curso', requirements: ['id' => '\d+'])]
    public function detalleCurso(
        Request $request,
        Curso $curso,
        ComentarioRepository $comentarioRepository,
        InscripcionRepository $inscripcionRepository
    ): Response {
        $comentarios = $comentarioRepository->buscarPorCurso($curso->getId());

        // Registra el curso visitado en el historial de la sesión para el recomendador
        $session = $request->getSession();
        $cursosClicados = $session->get('cursos_clicados_recientes', []);
        array_unshift($cursosClicados, $curso->getId());
        $cursosClicados = array_values(array_unique(array_map('intval', $cursosClicados)));
        $session->set('cursos_clicados_recientes', array_slice($cursosClicados, 0, 15));

        $inscripcionExistente = false;
        $puedeComentar = false;
        $usuario = $this->getUser();

        if ($usuario instanceof User) {
            $inscripcion = $inscripcionRepository->buscarInscripcionPorCursoYEstudiante($curso->getId(), $usuario);

            $inscripcionExistente = $inscripcion !== null;
            $puedeComentar = $inscripcion !== null;
        }

        return $this->render('paginas/cursos/detalle_curso.html.twig', [
            'curso' => $curso,
            'comentarios' => $comentarios,
            'inscripcionExistente' => $inscripcionExistente,
            'puedeComentar' => $puedeComentar,
        ]);
    }

    // Muestra el visor de contenido de un curso: Recursos, tareas y progreso del alumno
    // Para estudiantes y profesores, además exige una inscripción en estado activo o completado
    #[Route('/curso/visor/{id}', name: 'app_visor_curso', requirements: ['id' => '\d+'])]
    public function visorCurso(
        Curso $curso,
        InscripcionRepository $inscripcionRepository,
        RecursoCursoRepository $recursoCursoRepository,
        TareaCursoRepository $tareaCursoRepository,
        EntregaTareaRepository $entregaTareaRepository,
        RecursoVistoRepository $recursoVistoRepository
    ): Response {
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_ESTUDIANTE') &&
            !$this->isGranted('ROLE_PROFESOR')
        ) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        $inscripcion = null;
        $recursosVistosIds = [];
        $entregasPorTarea = [];

        // El admin accede al visor sin restricciones de inscripción
        if (!$this->isGranted('ROLE_ADMIN')) {
            $usuario = $this->getUser();

            if (!$usuario instanceof User) {
                return $this->redirectToRoute('app_login');
            }

            $inscripcion = $inscripcionRepository->buscarInscripcionPorCursoYEstudiante($curso->getId(), $usuario);

            if (!$inscripcion || !in_array($inscripcion->getEstado(), ['activa', 'completada'], true)) {
                throw $this->createAccessDeniedException('Debes tener la inscripción activa para acceder al visor del curso.');
            }

            // Recupera los IDs de los recursos ya completados por el alumno
            $recursosVistosIds = $recursoVistoRepository->buscarIdsRecursosVistosPorCurso($usuario, $curso->getId());

            // Construye el mapa de entregas por ID de tarea para acceder fácilmente desde la vista
            $entregas = $entregaTareaRepository->buscarEntregasDeEstudiantePorCurso($usuario, $curso->getId());
            foreach ($entregas as $entrega) {
                if ($entrega->getTarea()) {
                    $entregasPorTarea[$entrega->getTarea()->getId()] = $entrega;
                }
            }
        }

        $recursos = $recursoCursoRepository->buscarPorCurso($curso->getId());
        $tareas = $tareaCursoRepository->buscarPorCurso($curso->getId());

        return $this->render('paginas/cursos/visor_curso.html.twig', [
            'curso' => $curso,
            'inscripcion' => $inscripcion,
            'recursos' => $recursos,
            'recursosVistosIds' => $recursosVistosIds,
            'tareas' => $tareas,
            'entregasPorTarea' => $entregasPorTarea,
        ]);
    }
}