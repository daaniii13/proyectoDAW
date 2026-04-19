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

// Controlador de administración de usuarios
// Permite al administrador consultar el listado completo de usuarios,
// editar sus datos y rol y eliminarlos junto con todos sus registros relacionados
class AdminUsuariosController extends AbstractController
{
    // Muestra el listado de todos los usuarios del sistema ordenados por ID descendente
    #[Route('/admin/usuarios', name: 'app_admin_usuarios', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $usuarios = $userRepository->findBy([], ['id' => 'DESC']);

        return $this->render('paginas/admin/usuarios.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    // Actualiza el nombre, email, rol y estado activo de un usuario
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
            $this->addFlash('error', 'Nombre y correo son obligatorios.');
            return $this->redirectToRoute('app_admin_usuarios');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'El correo no es válido.');
            return $this->redirectToRoute('app_admin_usuarios');
        }

        // Comprueba que el email no pertenezca a otro usuario distinto
        $usuarioExistente = $userRepository->findOneBy(['email' => $email]);
        if ($usuarioExistente && $usuarioExistente->getId() !== $usuario->getId()) {
            $this->addFlash('error', 'Ya existe otro usuario con ese correo.');
            return $this->redirectToRoute('app_admin_usuarios');
        }

        // Si el rol no es válido asigna el rol más básico como medida de seguridad
        if (!in_array($rol, ['ROLE_ADMIN', 'ROLE_PROFESOR', 'ROLE_ESTUDIANTE'], true)) {
            $rol = 'ROLE_ESTUDIANTE';
        }

        $usuario->setNombre($nombre);
        $usuario->setEmail($email);
        $usuario->setRoles([$rol]);
        $usuario->setActivo($activo);

        $entityManager->flush();

        $this->addFlash('success', 'Usuario actualizado correctamente.');

        return $this->redirectToRoute('app_admin_usuarios');
    }

    // Elimina un usuario y todos sus datos relacionados de la base de datos
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

        // Evita que el administrador pueda eliminar su propia cuenta
        if ($adminActual && $adminActual->getId() === $usuario->getId()) {
            $this->addFlash('error', 'No puedes eliminar tu propia cuenta de administrador.');
            return $this->redirectToRoute('app_admin_usuarios');
        }

        $userId = $usuario->getId();
        $schemaManager = $connection->createSchemaManager();

        $connection->beginTransaction();

        try {
            // Desactiva temporalmente las restricciones de clave foránea de MySQL para poder eliminar registros en cualquier orden
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

            // Recupera los IDs de los cursos que el usuario creó como profesor
            $cursoIds = $connection->fetchFirstColumn(
                'SELECT id FROM curso WHERE profesor_id = ?',
                [$userId]
            );

            // Si el usuario tenía cursos como profesor elimina todos sus datos
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

            // Elimina los registros del usuario como alumno o participante en otras tablas
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

            // Elimina finalmente el registro del propio usuario
            $connection->executeStatement('DELETE FROM user WHERE id = ?', [$userId]);

            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
            $connection->commit();

            $this->addFlash('success', 'Usuario eliminado correctamente.');

        } catch (\Throwable $e) {
            // Ante cualquier error reactiva las claves foráneas y revierte la transacción
            try {
                $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
            } catch (\Throwable) {
            }

            $connection->rollBack();
            $this->addFlash('error', 'No se pudo eliminar el usuario.');
        }

        return $this->redirectToRoute('app_admin_usuarios');
    }
}