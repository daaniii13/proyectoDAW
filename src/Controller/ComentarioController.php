<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\Curso;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de comentarios en las páginas de detalle de curso
// Permite a los usuarios autenticados crear, editar y eliminar sus propios comentarios
class ComentarioController extends AbstractController
{
    // Publica un nuevo comentario en la página de detalle de un curso
    #[Route('/comentario/crear/{id}', name: 'app_comentario_crear', methods: ['POST'])]
    public function crear(
        Curso $curso,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_ESTUDIANTE') &&
            !$this->isGranted('ROLE_PROFESOR')
        ) {
            throw $this->createAccessDeniedException('No tienes permisos para comentar.');
        }

        if (!$this->isCsrfTokenValid('nuevo_comentario', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $contenido = trim((string) $request->request->get('contenido'));

        if ($contenido === '') {
            $this->addFlash('error', 'El comentario no puede estar vacío.');
            return $this->redirectToRoute('app_detalle_curso', ['id' => $curso->getId()]);
        }

        $comentario = new Comentario();
        $comentario->setCurso($curso);
        $comentario->setUsuario($usuario);
        $comentario->setContenido($contenido);

        $entityManager->persist($comentario);
        $entityManager->flush();

        $this->addFlash('success', 'Comentario publicado correctamente.');

        return $this->redirectToRoute('app_detalle_curso', ['id' => $curso->getId()]);
    }

    // Actualiza el contenido de un comentario existente
    #[Route('/comentario/editar/{id}', name: 'app_comentario_editar', methods: ['POST'])]
    public function editar(
        Comentario $comentario,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (
            $comentario->getUsuario()?->getId() !== $usuario->getId() &&
            !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException('No puedes editar este comentario.');
        }

        if (!$this->isCsrfTokenValid('editar_comentario_' . $comentario->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $contenido = trim((string) $request->request->get('contenido'));

        if ($contenido === '') {
            $this->addFlash('error', 'El comentario no puede quedar vacío.');
            return $this->redirectToRoute('app_detalle_curso', ['id' => $comentario->getCurso()->getId()]);
        }

        $comentario->setContenido($contenido);
        $entityManager->flush();

        $this->addFlash('success', 'Comentario actualizado correctamente.');

        return $this->redirectToRoute('app_detalle_curso', ['id' => $comentario->getCurso()->getId()]);
    }

    // Elimina un comentario de la base de datos. Solo puede eliminarlo su autor o el administrador
    #[Route('/comentario/eliminar/{id}', name: 'app_comentario_eliminar', methods: ['POST'])]
    public function eliminar(
        Comentario $comentario,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (
            $comentario->getUsuario()?->getId() !== $usuario->getId() &&
            !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException('No puedes eliminar este comentario.');
        }

        if (!$this->isCsrfTokenValid('eliminar_comentario_' . $comentario->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        // Guarda el ID antes de eliminar el comentario, ya que tras el remove no se podrá acceder al curso
        $cursoId = $comentario->getCurso()->getId();

        $entityManager->remove($comentario);
        $entityManager->flush();

        $this->addFlash('success', 'Comentario eliminado correctamente.');

        return $this->redirectToRoute('app_detalle_curso', ['id' => $cursoId]);
    }

    // Alterna el estado destacado de un comentario
    #[Route('/comentario/destacar/{id}', name: 'app_comentario_destacar', methods: ['POST'])]
    public function destacar(
        Comentario $comentario,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $curso = $comentario->getCurso();
        $esProfesorDelCurso = $curso->getProfesor()?->getId() === $usuario->getId();

        // Solo el profesor propietario del curso o el admin pueden destacar comentarios
        if (!$esProfesorDelCurso && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No puedes destacar comentarios de este curso.');
        }

        if (!$this->isCsrfTokenValid('destacar_comentario_' . $comentario->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        // Invierte el estado: Si estaba destacado lo quitamos y viceversa
        $comentario->setDestacado(!$comentario->isDestacado());
        $entityManager->flush();

        $this->addFlash(
            'success',
            $comentario->isDestacado()
                ? 'Comentario destacado correctamente.'
                : 'Comentario quitado de destacados correctamente.'
        );

        return $this->redirectToRoute('app_detalle_curso', ['id' => $curso->getId()]);
    }
}