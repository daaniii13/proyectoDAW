<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\EntregaTarea;
use App\Entity\TareaCurso;
use App\Entity\User;
use App\Repository\EntregaTareaRepository;
use App\Repository\InscripcionRepository;
use App\Repository\TareaCursoRepository;
use App\Service\ProgresoCursoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de gestión de tareas y entregas de un curso
// Agrupa las acciones del profesor (crear, editar, eliminar tareas y corregir entregas)
// y las del alumno (entregar una tarea y visualizar el archivo de entrega)
class TareaCursoController extends AbstractController
{
    // Lista las tareas del curso y las entregas agrupadas por tarea para el panel del profesor
    // Procesa la creación de una nueva tarea, validando el tipo MIME del archivo y la fecha límite si se indica
    #[Route('/profesor/curso/{id}/tareas', name: 'app_profesor_tareas_curso', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function gestionar(
        Curso $curso,
        Request $request,
        EntityManagerInterface $entityManager,
        TareaCursoRepository $tareaCursoRepository,
        EntregaTareaRepository $entregaTareaRepository,
        ProgresoCursoService $progresoCursoService
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROFESOR')) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (
                !$usuario instanceof User ||
                !$curso->getProfesor() ||
                $curso->getProfesor()->getId() !== $usuario->getId()
            )
        ) {
            throw $this->createAccessDeniedException('No puedes gestionar tareas de un curso que no es tuyo.');
        }

        $errorTarea = null;
        $datosTarea = [
            'titulo' => '',
            'descripcion' => '',
            'fecha_limite' => '',
        ];

        if ($request->isMethod('POST') && $request->request->get('formulario') === 'crear_tarea') {
            if (!$this->isCsrfTokenValid('crear_tarea_' . $curso->getId(), (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF no válido.');
            }

            $titulo = trim((string) $request->request->get('titulo_tarea'));
            $descripcion = trim((string) $request->request->get('descripcion_tarea'));
            $fechaLimiteRaw = trim((string) $request->request->get('fecha_limite_tarea'));

            $datosTarea = [
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'fecha_limite' => $fechaLimiteRaw,
            ];

            if ($titulo === '' || $descripcion === '') {
                $errorTarea = 'El título y la descripción de la tarea son obligatorios.';
            } else {
                $tarea = new TareaCurso();
                $tarea->setCurso($curso);
                $tarea->setTitulo($titulo);
                $tarea->setDescripcion($descripcion);

                if ($fechaLimiteRaw !== '') {
                    $fechaLimite = \DateTime::createFromFormat('Y-m-d\TH:i', $fechaLimiteRaw);
                    if ($fechaLimite !== false) {
                        $tarea->setFechaLimite($fechaLimite);
                    }
                }

                $archivoProfesor = $request->files->get('archivo_profesor');
                if ($archivoProfesor) {
                    $errorArchivo = $this->guardarArchivoProfesor($archivoProfesor, $tarea);
                    if ($errorArchivo !== null) {
                        $errorTarea = $errorArchivo;
                    }
                }

                if ($errorTarea === null) {
                    $entityManager->persist($tarea);
                    $entityManager->flush();

                    // Recalcula el progreso de todos los inscritos al añadir una nueva tarea
                    $progresoCursoService->recalcularParaTodos($curso->getId());

                    $this->addFlash('success', 'Tarea creada correctamente.');

                    return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
                }
            }
        }

        $tareas = $tareaCursoRepository->buscarPorCurso($curso->getId());
        $entregas = $entregaTareaRepository->buscarEntregasPorCurso($curso->getId());

        // Agrupa las entregas por ID de tarea para facilitar el acceso desde la vista
        $entregasPorTarea = [];
        foreach ($entregas as $entrega) {
            $tareaId = $entrega->getTarea()?->getId();
            if ($tareaId === null) {
                continue;
            }
            if (!isset($entregasPorTarea[$tareaId])) {
                $entregasPorTarea[$tareaId] = [];
            }
            $entregasPorTarea[$tareaId][] = $entrega;
        }

        return $this->render('paginas/profesor/tareas_curso.html.twig', [
            'curso' => $curso,
            'tareas' => $tareas,
            'errorTarea' => $errorTarea,
            'datosTarea' => $datosTarea,
            'entregasPorTarea' => $entregasPorTarea,
        ]);
    }

    // Actualiza los datos de una tarea existente (título, descripción, fecha límite y archivo)
    // Si se sube un archivo nuevo elimina el anterior del sistema de archivos
    #[Route('/profesor/tarea/{id}/editar', name: 'app_profesor_editar_tarea', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function editar(
        TareaCurso $tarea,
        Request $request,
        EntityManagerInterface $entityManager,
        ProgresoCursoService $progresoCursoService
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROFESOR')) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();
        $curso = $tarea->getCurso();

        if (!$curso) {
            throw $this->createNotFoundException('La tarea no pertenece a ningún curso.');
        }

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (
                !$usuario instanceof User ||
                !$curso->getProfesor() ||
                $curso->getProfesor()->getId() !== $usuario->getId()
            )
        ) {
            throw $this->createAccessDeniedException('No puedes editar tareas de un curso que no es tuyo.');
        }

        if (!$this->isCsrfTokenValid('editar_tarea_' . $tarea->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $titulo = trim((string) $request->request->get('titulo_tarea'));
        $descripcion = trim((string) $request->request->get('descripcion_tarea'));
        $fechaLimiteRaw = trim((string) $request->request->get('fecha_limite_tarea'));

        if ($titulo === '' || $descripcion === '') {
            $this->addFlash('error', 'El título y la descripción de la tarea son obligatorios.');
            return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
        }

        $tarea->setTitulo($titulo);
        $tarea->setDescripcion($descripcion);

        if ($fechaLimiteRaw !== '') {
            $fechaLimite = \DateTime::createFromFormat('Y-m-d\TH:i', $fechaLimiteRaw);
            if ($fechaLimite === false) {
                $this->addFlash('error', 'La fecha límite no es válida.');
                return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
            }
            $tarea->setFechaLimite($fechaLimite);
        } else {
            // Si el campo de fecha se deja vacío elimina la fecha límite
            $tarea->setFechaLimite(null);
        }

        $archivoProfesor = $request->files->get('archivo_profesor');
        if ($archivoProfesor) {
            $archivoAnterior = $tarea->getArchivoProfesor();
            $errorArchivo = $this->guardarArchivoProfesor($archivoProfesor, $tarea);

            if ($errorArchivo !== null) {
                $this->addFlash('error', $errorArchivo);
                return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
            }

            // Elimina el archivo anterior del servidor si existía
            if ($archivoAnterior) {
                $rutaAnterior = $this->getParameter('kernel.project_dir') . '/public/uploads/tareas/' . $archivoAnterior;
                if (is_file($rutaAnterior)) {
                    @unlink($rutaAnterior);
                }
            }
        }

        $entityManager->flush();
        $progresoCursoService->recalcularParaTodos($curso->getId());

        $this->addFlash('success', 'Tarea actualizada correctamente.');

        return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
    }

    // El profesor corrige una entrega asignándole una nota entre 0 y 100 y un comentario opcional
    #[Route('/profesor/entrega/{id}/corregir', name: 'app_profesor_corregir_entrega', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function corregir(
        EntregaTarea $entrega,
        Request $request,
        EntityManagerInterface $entityManager,
        ProgresoCursoService $progresoCursoService
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROFESOR')) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();
        $curso = $entrega->getTarea()?->getCurso();

        if (!$curso) {
            throw $this->createNotFoundException('La entrega no está asociada a un curso.');
        }

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (
                !$usuario instanceof User ||
                !$curso->getProfesor() ||
                $curso->getProfesor()->getId() !== $usuario->getId()
            )
        ) {
            throw $this->createAccessDeniedException('No puedes corregir entregas de un curso que no es tuyo.');
        }

        if (!$this->isCsrfTokenValid('corregir_entrega_' . $entrega->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $nota = filter_var($request->request->get('nota'), FILTER_VALIDATE_INT);
        $comentarioProfesor = trim((string) $request->request->get('comentario_profesor'));

        if ($nota === false || $nota < 0 || $nota > 100) {
            $this->addFlash('error', 'La nota debe ser un número entre 0 y 100.');
            return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
        }

        $entrega->setNota($nota);
        $entrega->setComentarioProfesor($comentarioProfesor !== '' ? $comentarioProfesor : null);
        $entrega->setFechaRevision(new \DateTime());

        // La entrega se aprueba con 50 o más, por debajo queda suspensa
        if ($nota >= 50) {
            $entrega->setEstadoRevision('aprobada');
            $this->addFlash('success', 'Entrega corregida y aprobada.');
        } else {
            $entrega->setEstadoRevision('suspensa');
            $this->addFlash('success', 'Entrega corregida. El alumno deberá repetir la tarea.');
        }

        $entityManager->flush();

        // Recalcula el progreso únicamente del alumno que realizó la entrega
        $estudiante = $entrega->getEstudiante();
        if ($estudiante instanceof User) {
            $progresoCursoService->recalcular($curso->getId(), $estudiante);
        }

        return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
    }

    // El alumno sube un archivo como respuesta a una tarea
    // Verifica que la inscripción esté activa, que el plazo no haya vencido,
    // que las tareas anteriores estén aprobadas y que el archivo tenga un MIME permitido
    #[Route('/tarea/{id}/entregar', name: 'app_entregar_tarea', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function entregar(
        TareaCurso $tarea,
        Request $request,
        EntregaTareaRepository $entregaTareaRepository,
        TareaCursoRepository $tareaCursoRepository,
        InscripcionRepository $inscripcionRepository,
        EntityManagerInterface $entityManager,
        ProgresoCursoService $progresoCursoService
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('entregar_tarea_' . $tarea->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $curso = $tarea->getCurso();

        if (!$curso) {
            throw $this->createNotFoundException('La tarea no pertenece a ningún curso.');
        }

        $inscripcion = $inscripcionRepository->buscarInscripcionPorCursoYEstudiante($curso->getId(), $usuario);

        if (!$inscripcion || !in_array($inscripcion->getEstado(), ['activa', 'completada'], true)) {
            throw $this->createAccessDeniedException('No puedes entregar tareas en este curso.');
        }

        // Comprueba que el plazo de entrega no haya vencido
        if ($tarea->getFechaLimite() !== null) {
            $ahora = new \DateTimeImmutable();
            $fechaLimite = \DateTimeImmutable::createFromInterface($tarea->getFechaLimite());

            if ($ahora > $fechaLimite) {
                $this->addFlash('error', 'El plazo de entrega de esta tarea ha finalizado.');
                return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
            }
        }

        // Verifica que las tareas anteriores estén aprobadas
        $tareasCurso = $tareaCursoRepository->buscarPorCurso($curso->getId());

        foreach ($tareasCurso as $tareaAnterior) {
            if ($tareaAnterior->getId() >= $tarea->getId()) {
                continue;
            }

            $entregaAnterior = $entregaTareaRepository->buscarUnaEntrega($tareaAnterior->getId(), $usuario);

            if (!$entregaAnterior || $entregaAnterior->getEstadoRevision() !== 'aprobada') {
                $this->addFlash('error', 'Debes tener aprobadas las tareas anteriores para continuar.');
                return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
            }
        }

        $archivoEntrega = $request->files->get('archivo_entrega');
        $comentario = trim((string) $request->request->get('comentario_entrega'));

        if (!$archivoEntrega) {
            $this->addFlash('error', 'Debes adjuntar un archivo para entregar la tarea.');
            return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
        }

        $mime = (string) $archivoEntrega->getMimeType();
        $mimePermitidos = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'application/zip',
            'application/x-zip-compressed',
        ];

        if (!in_array($mime, $mimePermitidos, true)) {
            $this->addFlash('error', 'El archivo entregado no tiene un formato permitido.');
            return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
        }

        $directorioDestino = $this->getParameter('kernel.project_dir') . '/public/uploads/entregas';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0775, true);
        }

        $extension = $archivoEntrega->guessExtension();
        $nombreArchivo = $extension
            ? uniqid('entrega_', true) . '.' . $extension
            : uniqid('entrega_', true) . '.bin';

        try {
            $archivoEntrega->move($directorioDestino, $nombreArchivo);
        } catch (FileException $e) {
            $this->addFlash('error', 'No se pudo guardar la entrega.');
            return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
        }

        // Si ya existe una entrega previa la reutiliza, si no, crea una nueva
        $entrega = $entregaTareaRepository->buscarUnaEntrega($tarea->getId(), $usuario);

        if (!$entrega) {
            $entrega = new EntregaTarea();
            $entrega->setTarea($tarea);
            $entrega->setEstudiante($usuario);
            $entityManager->persist($entrega);
        }

        $entrega->setArchivoEntrega($nombreArchivo);
        $entrega->setComentario($comentario !== '' ? $comentario : null);
        $entrega->setFechaEntrega(new \DateTime());
        // Al volver a entregar, resetea el estado de revisión para que el profesor la vuelva a corregir
        $entrega->setEstadoRevision('pendiente');
        $entrega->setNota(null);
        $entrega->setComentarioProfesor(null);
        $entrega->setFechaRevision(null);

        $entityManager->flush();

        $progresoCursoService->recalcular($curso->getId(), $usuario);

        $this->addFlash('success', 'Tarea enviada correctamente. Queda pendiente de corrección.');

        return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
    }

    // Elimina una tarea y su archivo físico asociado si lo tiene
    // Recalcula el progreso de todos los inscritos tras la eliminación
    #[Route('/profesor/tarea/{id}/eliminar', name: 'app_profesor_eliminar_tarea', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function eliminar(
        TareaCurso $tarea,
        Request $request,
        EntityManagerInterface $entityManager,
        ProgresoCursoService $progresoCursoService
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROFESOR')) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();
        $curso = $tarea->getCurso();

        if (!$curso) {
            throw $this->createNotFoundException('La tarea no pertenece a ningún curso.');
        }

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (
                !$usuario instanceof User ||
                !$curso->getProfesor() ||
                $curso->getProfesor()->getId() !== $usuario->getId()
            )
        ) {
            throw $this->createAccessDeniedException('No puedes eliminar tareas de un curso que no es tuyo.');
        }

        if (!$this->isCsrfTokenValid('eliminar_tarea_' . $tarea->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        // Guarda la ruta del archivo antes de eliminar la tarea
        $rutaArchivo = null;
        if ($tarea->getArchivoProfesor()) {
            $rutaArchivo = $this->getParameter('kernel.project_dir') . '/public/uploads/tareas/' . $tarea->getArchivoProfesor();
        }

        $cursoId = $curso->getId();

        $entityManager->remove($tarea);
        $entityManager->flush();

        // Elimina el archivo físico después de confirmar la eliminación en la base de datos
        if ($rutaArchivo && is_file($rutaArchivo)) {
            @unlink($rutaArchivo);
        }

        $progresoCursoService->recalcularParaTodos($cursoId);

        $this->addFlash('success', 'Tarea eliminada correctamente.');

        return $this->redirectToRoute('app_profesor_tareas_curso', [
            'id' => $cursoId,
        ]);
    }

    // Permite ver el archivo de una entrega directamente en el navegador
    // Solo accesible para el profesor del curso o el administrador
    #[Route('/profesor/entrega/{id}/ver', name: 'app_profesor_ver_entrega', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function verEntrega(EntregaTarea $entrega): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROFESOR')) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();
        $curso = $entrega->getTarea()?->getCurso();

        if (!$curso) {
            throw $this->createNotFoundException('La entrega no está asociada a un curso.');
        }

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (
                !$usuario instanceof User ||
                !$curso->getProfesor() ||
                $curso->getProfesor()->getId() !== $usuario->getId()
            )
        ) {
            throw $this->createAccessDeniedException('No puedes ver entregas de un curso que no es tuyo.');
        }

        $nombreArchivo = $entrega->getArchivoEntrega();

        if (!$nombreArchivo) {
            throw $this->createNotFoundException('La entrega no tiene archivo asociado.');
        }

        $rutaArchivo = $this->getParameter('kernel.project_dir') . '/public/uploads/entregas/' . $nombreArchivo;

        if (!is_file($rutaArchivo)) {
            throw $this->createNotFoundException('El archivo de la entrega no existe en el servidor.');
        }

        // Devuelve el archivo para que se muestre directamente en el navegador (no como descarga)
        $response = new BinaryFileResponse($rutaArchivo);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            basename($rutaArchivo)
        );

        return $response;
    }

    // Valida el tipo MIME del archivo del profesor, crea el directorio si no existe
    // y mueve el archivo con un nombre único. Devuelve null si todo fue correcto
    // o el mensaje de error en caso contrario
    private function guardarArchivoProfesor(mixed $archivoProfesor, TareaCurso $tarea): ?string
    {
        $mime = (string) $archivoProfesor->getMimeType();
        $mimePermitidos = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'application/zip',
            'application/x-zip-compressed',
        ];

        if (!in_array($mime, $mimePermitidos, true)) {
            return 'El archivo de la tarea no tiene un formato permitido.';
        }

        $directorioDestino = $this->getParameter('kernel.project_dir') . '/public/uploads/tareas';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0775, true);
        }

        $extension = $archivoProfesor->guessExtension();
        $nombreArchivo = $extension
            ? uniqid('tarea_', true) . '.' . $extension
            : uniqid('tarea_', true) . '.bin';

        try {
            $archivoProfesor->move($directorioDestino, $nombreArchivo);
            $tarea->setArchivoProfesor($nombreArchivo);
        } catch (FileException $e) {
            return 'No se pudo guardar el archivo de la tarea.';
        }

        return null;
    }
}