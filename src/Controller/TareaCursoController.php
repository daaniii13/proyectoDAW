<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\EntregaTarea;
use App\Entity\MensajeEntregaTarea;
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

class TareaCursoController extends AbstractController
{
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

                    $progresoCursoService->recalcularParaTodos($curso->getId());

                    $this->addFlash('success', 'Tarea creada correctamente.');

                    return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
                }
            }
        }

        $tareas = $tareaCursoRepository->buscarPorCurso($curso->getId());
        $entregas = $entregaTareaRepository->buscarEntregasPorCurso($curso->getId());

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

        if ($comentarioProfesor !== '') {
            $mensaje = new MensajeEntregaTarea();
            $mensaje->setEntrega($entrega);
            $mensaje->setAutor($usuario);
            $mensaje->setContenido($comentarioProfesor);

            $entityManager->persist($mensaje);
        }

        if ($nota >= 50) {
            $entrega->setEstadoRevision('aprobada');
            $this->addFlash('success', 'Entrega corregida y aprobada.');
        } else {
            $entrega->setEstadoRevision('suspensa');
            $this->addFlash('success', 'Entrega corregida. El alumno deberá repetir la tarea.');
        }

        $entityManager->flush();

        $estudiante = $entrega->getEstudiante();

        if ($estudiante instanceof User) {
            $progresoCursoService->recalcular($curso->getId(), $estudiante);
        }

        return $this->redirectToRoute('app_profesor_tareas_curso', ['id' => $curso->getId()]);
    }

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

        if ($tarea->getFechaLimite() !== null) {
            $ahora = new \DateTimeImmutable();
            $fechaLimite = \DateTimeImmutable::createFromInterface($tarea->getFechaLimite());

            if ($ahora > $fechaLimite) {
                $this->addFlash('error', 'El plazo de entrega de esta tarea ha finalizado.');
                return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
            }
        }

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
        $entrega = $entregaTareaRepository->buscarUnaEntrega($tarea->getId(), $usuario);

        if ($entrega && $entrega->getEstadoRevision() === 'aprobada') {
            $this->addFlash('error', 'Esta entrega ya fue aprobada y no puede modificarse.');
            return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
        }

        if (!$entrega && !$archivoEntrega) {
            $this->addFlash('error', 'Debes adjuntar un archivo para realizar la primera entrega.');
            return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
        }

        if ($entrega && !$archivoEntrega && $comentario === '') {
            $this->addFlash('error', 'Debes adjuntar un archivo o escribir un mensaje.');
            return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
        }

        $nombreArchivo = null;

        if ($archivoEntrega) {
            $mime = (string) $archivoEntrega->getMimeType();
            $extensionOriginal = strtolower((string) $archivoEntrega->getClientOriginalExtension());

            $mimePermitidos = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
                'application/zip',
                'application/x-zip-compressed',
                'application/octet-stream',
            ];

            $extensionesPermitidas = [
                'pdf',
                'doc',
                'docx',
                'jpg',
                'jpeg',
                'png',
                'zip',
            ];

            if (
                !in_array($mime, $mimePermitidos, true)
                && !in_array($extensionOriginal, $extensionesPermitidas, true)
            ) {
                $this->addFlash('error', 'El archivo entregado no tiene un formato permitido.');
                return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
            }

            $directorioDestino = $this->getParameter('kernel.project_dir') . '/public/uploads/entregas';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0775, true);
            }

            $extension = $extensionOriginal ?: $archivoEntrega->guessExtension();

            if (!$extension) {
                $extension = 'bin';
            }

            $nombreArchivo = uniqid('entrega_', true) . '.' . $extension;

            try {
                $archivoEntrega->move($directorioDestino, $nombreArchivo);
            } catch (FileException $e) {
                $this->addFlash('error', 'No se pudo guardar la entrega.');
                return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
            }
        }

        if (!$entrega) {
            $entrega = new EntregaTarea();
            $entrega->setTarea($tarea);
            $entrega->setEstudiante($usuario);
            $entityManager->persist($entrega);
        }

        if ($nombreArchivo !== null) {
            $entrega->setArchivoEntrega($nombreArchivo);
            $entrega->setFechaEntrega(new \DateTime());
            $entrega->setEstadoRevision('pendiente');
            $entrega->setNota(null);
            $entrega->setComentarioProfesor(null);
            $entrega->setFechaRevision(null);
        }

        $entrega->setComentario($comentario !== '' ? $comentario : $entrega->getComentario());

        if ($comentario !== '') {
            $mensaje = new MensajeEntregaTarea();
            $mensaje->setEntrega($entrega);
            $mensaje->setAutor($usuario);
            $mensaje->setContenido($comentario);

            $entityManager->persist($mensaje);
        }

        $entityManager->flush();

        $progresoCursoService->recalcular($curso->getId(), $usuario);

        $this->addFlash('success', 'Tarea enviada correctamente. Queda pendiente de corrección.');

        return $this->redirectToRoute('app_visor_curso', ['id' => $curso->getId()]);
    }

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

        $rutaArchivo = null;

        if ($tarea->getArchivoProfesor()) {
            $rutaArchivo = $this->getParameter('kernel.project_dir') . '/public/uploads/tareas/' . $tarea->getArchivoProfesor();
        }

        $cursoId = $curso->getId();

        $entityManager->remove($tarea);
        $entityManager->flush();

        if ($rutaArchivo && is_file($rutaArchivo)) {
            @unlink($rutaArchivo);
        }

        $progresoCursoService->recalcularParaTodos($cursoId);

        $this->addFlash('success', 'Tarea eliminada correctamente.');

        return $this->redirectToRoute('app_profesor_tareas_curso', [
            'id' => $cursoId,
        ]);
    }

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

        $response = new BinaryFileResponse($rutaArchivo);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            basename($rutaArchivo)
        );

        return $response;
    }

    private function guardarArchivoProfesor(mixed $archivoProfesor, TareaCurso $tarea): ?string
    {
        $mime = (string) $archivoProfesor->getMimeType();
        $extensionOriginal = strtolower((string) $archivoProfesor->getClientOriginalExtension());

        $mimePermitidos = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream',
        ];

        $extensionesPermitidas = [
            'pdf',
            'doc',
            'docx',
            'jpg',
            'jpeg',
            'png',
            'zip',
        ];

        if (
            !in_array($mime, $mimePermitidos, true)
            && !in_array($extensionOriginal, $extensionesPermitidas, true)
        ) {
            return 'El archivo de la tarea no tiene un formato permitido.';
        }

        $directorioDestino = $this->getParameter('kernel.project_dir') . '/public/uploads/tareas';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0775, true);
        }

        $extension = $extensionOriginal ?: $archivoProfesor->guessExtension();

        if (!$extension) {
            $extension = 'bin';
        }

        $nombreArchivo = uniqid('tarea_', true) . '.' . $extension;

        try {
            $archivoProfesor->move($directorioDestino, $nombreArchivo);
            $tarea->setArchivoProfesor($nombreArchivo);
        } catch (FileException $e) {
            return 'No se pudo guardar el archivo de la tarea.';
        }

        return null;
    }
}