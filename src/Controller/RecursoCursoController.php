<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\RecursoCurso;
use App\Entity\User;
use App\Repository\RecursoCursoRepository;
use App\Service\ProgresoCursoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de gestión de recursos de un curso
// Permite al profesor y al administrador crear, editar y eliminar los recursos de un curso
class RecursoCursoController extends AbstractController
{
    // Lista los recursos del curso y procesa la creación de uno nuevo
    // Valida el tipo MIME del archivo adjunto si se sube uno
    #[Route('/profesor/curso/{id}/recursos', name: 'app_profesor_recursos_curso', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function gestionar(
        Curso $curso,
        Request $request,
        EntityManagerInterface $entityManager,
        RecursoCursoRepository $recursoCursoRepository,
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
            throw $this->createAccessDeniedException('No puedes gestionar recursos de un curso que no es tuyo.');
        }

        $errorRecurso = null;
        $datosRecurso = [
            'titulo' => '',
            'tipo' => '',
            'contenido' => '',
            'orden' => '',
        ];

        if ($request->isMethod('POST') && $request->request->get('formulario') === 'crear_recurso') {
            if (!$this->isCsrfTokenValid('crear_recurso_' . $curso->getId(), (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF no válido.');
            }

            $titulo = trim((string) $request->request->get('titulo_recurso'));
            $tipo = trim((string) $request->request->get('tipo_recurso'));
            $contenido = trim((string) $request->request->get('contenido_recurso'));
            $ordenRaw = trim((string) $request->request->get('orden_recurso'));

            $datosRecurso = [
                'titulo' => $titulo,
                'tipo' => $tipo,
                'contenido' => $contenido,
                'orden' => $ordenRaw,
            ];

            if ($titulo === '' || $tipo === '' || $contenido === '' || $ordenRaw === '') {
                $errorRecurso = 'Todos los campos obligatorios del recurso deben completarse.';
            } elseif (!in_array($tipo, ['texto', 'documento', 'enlace', 'video'], true)) {
                $errorRecurso = 'Debes seleccionar un tipo de recurso válido.';
            } elseif (!ctype_digit($ordenRaw) || (int) $ordenRaw < 1) {
                $errorRecurso = 'El orden debe ser un número entero mayor o igual que 1.';
            } else {
                $recurso = new RecursoCurso();
                $recurso->setCurso($curso);
                $recurso->setTitulo($titulo);
                $recurso->setTipo($tipo);
                $recurso->setContenido($contenido);
                $recurso->setOrden((int) $ordenRaw);

                $archivoRecurso = $request->files->get('archivo_recurso');
                if ($archivoRecurso) {
                    $errorArchivo = $this->guardarArchivoRecurso($archivoRecurso, $recurso);
                    if ($errorArchivo !== null) {
                        $errorRecurso = $errorArchivo;
                    }
                }

                if ($errorRecurso === null) {
                    $entityManager->persist($recurso);
                    $entityManager->flush();

                    // Recalcula el progreso de todos los inscritos al añadir un nuevo recurso
                    $progresoCursoService->recalcularParaTodos($curso->getId());

                    $this->addFlash('success', 'Recurso creado correctamente.');

                    return $this->redirectToRoute('app_profesor_recursos_curso', [
                        'id' => $curso->getId(),
                    ]);
                }
            }
        }

        return $this->render('paginas/profesor/recursos_curso.html.twig', [
            'curso' => $curso,
            'recursos' => $recursoCursoRepository->buscarPorCurso($curso->getId()),
            'errorRecurso' => $errorRecurso,
            'datosRecurso' => $datosRecurso,
        ]);
    }

    // Actualiza los datos de un recurso existente
    #[Route('/profesor/recurso/{id}/editar', name: 'app_profesor_editar_recurso', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function editar(
        RecursoCurso $recurso,
        Request $request,
        EntityManagerInterface $entityManager,
        ProgresoCursoService $progresoCursoService
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROFESOR')) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();
        $curso = $recurso->getCurso();

        if (!$curso) {
            throw $this->createNotFoundException('El recurso no está asociado a ningún curso.');
        }

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (
                !$usuario instanceof User ||
                !$curso->getProfesor() ||
                $curso->getProfesor()->getId() !== $usuario->getId()
            )
        ) {
            throw $this->createAccessDeniedException('No puedes editar recursos de un curso que no es tuyo.');
        }

        if (!$this->isCsrfTokenValid('editar_recurso_' . $recurso->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $titulo = trim((string) $request->request->get('titulo_recurso'));
        $tipo = trim((string) $request->request->get('tipo_recurso'));
        $contenido = trim((string) $request->request->get('contenido_recurso'));
        $ordenRaw = trim((string) $request->request->get('orden_recurso'));

        if ($titulo === '' || $tipo === '' || $contenido === '' || $ordenRaw === '') {
            $this->addFlash('error', 'Todos los campos obligatorios del recurso deben completarse.');
            return $this->redirectToRoute('app_profesor_recursos_curso', ['id' => $curso->getId()]);
        }

        if (!in_array($tipo, ['texto', 'documento', 'enlace', 'video'], true)) {
            $this->addFlash('error', 'Debes seleccionar un tipo de recurso válido.');
            return $this->redirectToRoute('app_profesor_recursos_curso', ['id' => $curso->getId()]);
        }

        if (!ctype_digit($ordenRaw) || (int) $ordenRaw < 1) {
            $this->addFlash('error', 'El orden debe ser un número entero mayor o igual que 1.');
            return $this->redirectToRoute('app_profesor_recursos_curso', ['id' => $curso->getId()]);
        }

        $recurso->setTitulo($titulo);
        $recurso->setTipo($tipo);
        $recurso->setContenido($contenido);
        $recurso->setOrden((int) $ordenRaw);

        $archivoRecurso = $request->files->get('archivo_recurso');
        if ($archivoRecurso) {
            $archivoAnterior = $recurso->getArchivoUrl();
            $errorArchivo = $this->guardarArchivoRecurso($archivoRecurso, $recurso);

            if ($errorArchivo !== null) {
                $this->addFlash('error', $errorArchivo);
                return $this->redirectToRoute('app_profesor_recursos_curso', ['id' => $curso->getId()]);
            }

            // Si el recurso tenía un archivo previo, se elimina del sistema de archivos
            if ($archivoAnterior) {
                $rutaAnterior = $this->getParameter('kernel.project_dir') . '/public/uploads/recursos/' . $archivoAnterior;
                if (is_file($rutaAnterior)) {
                    @unlink($rutaAnterior);
                }
            }
        }

        $entityManager->flush();
        $progresoCursoService->recalcularParaTodos($curso->getId());

        $this->addFlash('success', 'Recurso actualizado correctamente.');

        return $this->redirectToRoute('app_profesor_recursos_curso', [
            'id' => $curso->getId(),
        ]);
    }

    // Elimina un recurso del curso y su archivo físico si lo tiene
    #[Route('/profesor/recurso/{id}/eliminar', name: 'app_profesor_eliminar_recurso', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function eliminar(
        RecursoCurso $recurso,
        Request $request,
        EntityManagerInterface $entityManager,
        ProgresoCursoService $progresoCursoService
    ): Response {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PROFESOR')) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();
        $curso = $recurso->getCurso();

        if (!$curso) {
            throw $this->createNotFoundException('El recurso no está asociado a ningún curso.');
        }

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (
                !$usuario instanceof User ||
                !$curso->getProfesor() ||
                $curso->getProfesor()->getId() !== $usuario->getId()
            )
        ) {
            throw $this->createAccessDeniedException('No puedes eliminar recursos de un curso que no es tuyo.');
        }

        if (!$this->isCsrfTokenValid('eliminar_recurso_' . $recurso->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        // Guarda la ruta del archivo antes de eliminar el recurso
        $rutaArchivo = null;
        if ($recurso->getArchivoUrl()) {
            $rutaArchivo = $this->getParameter('kernel.project_dir') . '/public/uploads/recursos/' . $recurso->getArchivoUrl();
        }

        $cursoId = $curso->getId();

        $entityManager->remove($recurso);
        $entityManager->flush();

        // Elimina el archivo físico si existía, después de confirmar la eliminación en la base de datos
        if ($rutaArchivo && is_file($rutaArchivo)) {
            @unlink($rutaArchivo);
        }

        $progresoCursoService->recalcularParaTodos($cursoId);

        $this->addFlash('success', 'Recurso eliminado correctamente.');

        return $this->redirectToRoute('app_profesor_recursos_curso', [
            'id' => $cursoId,
        ]);
    }

    // Valida el tipo MIME del archivo subido, crea el directorio de destino si no existe,
    // genera un nombre único y mueve el archivo. Devuelve null si todo fue correcto
    // o el mensaje de error en caso contrario
    private function guardarArchivoRecurso(mixed $archivoRecurso, RecursoCurso $recurso): ?string
    {
        $mime = (string) $archivoRecurso->getMimeType();
        $mimePermitidos = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'application/zip',
            'application/x-zip-compressed',
            'video/mp4',
            'text/plain',
        ];

        if (!in_array($mime, $mimePermitidos, true)) {
            return 'El archivo del recurso no tiene un formato permitido.';
        }

        $directorioDestino = $this->getParameter('kernel.project_dir') . '/public/uploads/recursos';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0775, true);
        }

        $extension = $archivoRecurso->guessExtension();
        $nombreArchivo = $extension
            ? uniqid('recurso_', true) . '.' . $extension
            : uniqid('recurso_', true) . '.bin';

        try {
            $archivoRecurso->move($directorioDestino, $nombreArchivo);
            $recurso->setArchivoUrl($nombreArchivo);
        } catch (FileException $e) {
            return 'No se pudo guardar el archivo del recurso.';
        }

        return null;
    }
}