<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminUsuariosController extends AbstractController
{
    #[Route('/admin/usuarios', name: 'app_admin_usuarios', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $usuarios = $userRepository->findBy([], ['id' => 'DESC']);

        return $this->render('paginas/admin/usuarios.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    #[Route('/admin/usuarios/{id}/editar', name: 'app_admin_usuario_editar', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function editar(
        User $usuario,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('editar_usuario_' . $usuario->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $nombre = trim((string) $request->request->get('nombre'));
        $email = trim((string) $request->request->get('email'));
        $rol = trim((string) $request->request->get('rol'));
        $activo = $request->request->getBoolean('activo');

        if ($nombre === '' || $email === '') {
            $this->addFlash('error', 'admin.usuarios.flash_nombre_correo_obligatorios');
            return $this->redirectToRoute('app_admin_usuarios');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'admin.usuarios.flash_correo_no_valido');
            return $this->redirectToRoute('app_admin_usuarios');
        }

        $usuarioExistente = $userRepository->findOneBy(['email' => $email]);
        if ($usuarioExistente && $usuarioExistente->getId() !== $usuario->getId()) {
            $this->addFlash('error', 'admin.usuarios.flash_correo_duplicado');
            return $this->redirectToRoute('app_admin_usuarios');
        }

        if (!in_array($rol, ['ROLE_ADMIN', 'ROLE_PROFESOR', 'ROLE_ESTUDIANTE'], true)) {
            $rol = 'ROLE_ESTUDIANTE';
        }

        $usuario->setNombre($nombre);
        $usuario->setEmail($email);
        $usuario->setRoles([$rol]);
        $usuario->setActivo($activo);

        $entityManager->flush();

        $this->addFlash('success', 'admin.usuarios.flash_actualizado');

        return $this->redirectToRoute('app_admin_usuarios');
    }

    #[Route('/admin/usuarios/{id}/eliminar', name: 'app_admin_usuario_eliminar', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function eliminar(
        User $usuario,
        Request $request,
        EntityManagerInterface $entityManager,
        Connection $connection
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('eliminar_usuario_' . $usuario->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        /** @var User|null $adminActual */
        $adminActual = $this->getUser();

        if ($adminActual && $adminActual->getId() === $usuario->getId()) {
            $this->addFlash('error', 'admin.usuarios.flash_no_autoborrado');
            return $this->redirectToRoute('app_admin_usuarios');
        }

        $userId = $usuario->getId();
        $schemaManager = $connection->createSchemaManager();

        $connection->beginTransaction();

        try {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

            $cursoIds = $connection->fetchFirstColumn(
                'SELECT id FROM curso WHERE profesor_id = ?',
                [$userId]
            );

            if (!empty($cursoIds)) {
                $placeholders = implode(',', array_fill(0, count($cursoIds), '?'));

                $connection->executeStatement(
                    "DELETE FROM entrega_tarea WHERE tarea_id IN (SELECT id FROM tarea_curso WHERE curso_id IN ($placeholders))",
                    $cursoIds
                );

                $connection->executeStatement(
                    "DELETE FROM tarea_curso WHERE curso_id IN ($placeholders)",
                    $cursoIds
                );

                $connection->executeStatement(
                    "DELETE FROM recurso_visto WHERE recurso_id IN (SELECT id FROM recurso_curso WHERE curso_id IN ($placeholders))",
                    $cursoIds
                );

                $connection->executeStatement(
                    "DELETE FROM recurso_curso WHERE curso_id IN ($placeholders)",
                    $cursoIds
                );

                $connection->executeStatement(
                    "DELETE FROM comentario WHERE curso_id IN ($placeholders)",
                    $cursoIds
                );

                $connection->executeStatement(
                    "DELETE FROM inscripcion WHERE curso_id IN ($placeholders)",
                    $cursoIds
                );

                $connection->executeStatement(
                    "DELETE FROM curso WHERE id IN ($placeholders)",
                    $cursoIds
                );
            }

            if ($schemaManager->tablesExist(['comentario'])) {
                $connection->executeStatement('DELETE FROM comentario WHERE usuario_id = ?', [$userId]);
            }

            if ($schemaManager->tablesExist(['recurso_visto'])) {
                $connection->executeStatement('DELETE FROM recurso_visto WHERE usuario_id = ?', [$userId]);
            }

            if ($schemaManager->tablesExist(['entrega_tarea'])) {
                $connection->executeStatement('DELETE FROM entrega_tarea WHERE estudiante_id = ?', [$userId]);
            }

            if ($schemaManager->tablesExist(['inscripcion'])) {
                $connection->executeStatement('DELETE FROM inscripcion WHERE estudiante_id = ?', [$userId]);
            }

            if ($schemaManager->tablesExist(['suscripcion_profesor'])) {
                $connection->executeStatement('DELETE FROM suscripcion_profesor WHERE profesor_id = ?', [$userId]);
            }

            if ($schemaManager->tablesExist(['password_reset_request'])) {
                $connection->executeStatement('DELETE FROM password_reset_request WHERE usuario_id = ?', [$userId]);
            }

            if ($schemaManager->tablesExist(['recuperacion_contrasena'])) {
                $connection->executeStatement('DELETE FROM recuperacion_contrasena WHERE usuario_id = ?', [$userId]);
            }

            $connection->executeStatement('DELETE FROM user WHERE id = ?', [$userId]);

            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
            $connection->commit();

            $this->addFlash('success', 'admin.usuarios.flash_eliminado');
        } catch (\Throwable $e) {
            try {
                $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
            } catch (\Throwable) {
            }

            $connection->rollBack();
            $this->addFlash('error', 'admin.usuarios.flash_no_eliminado');
        }

        return $this->redirectToRoute('app_admin_usuarios');
    }
}