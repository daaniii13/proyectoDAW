<?php

namespace App\Controller;

use App\Entity\SuscripcionProfesor;
use App\Entity\User;
use App\Repository\SuscripcionProfesorRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de ajustes del perfil del usuario autenticado
// Agrupa las acciones relacionadas con la cuenta personal: Visualización del perfil, actualización 
// de datos personales y foto, cambio de contraseña, y solicitud de conversión a cuenta de profesor
class AjustesController extends AbstractController
{
    // Muestra la página de ajustes con los datos del usuario y su suscripción docente si la tiene
    #[Route('/ajustes', name: 'app_ajustes', methods: ['GET'])]
    public function index(SuscripcionProfesorRepository $suscripcionProfesorRepository): Response
    {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $suscripcionProfesorAjustes = $suscripcionProfesorRepository->buscarDeProfesor($usuario);

        return $this->render('paginas/ajustes/index.html.twig', [
            'usuarioActual' => $usuario,
            'suscripcionProfesorAjustes' => $suscripcionProfesorAjustes,
            'esProfesor' => $this->isGranted('ROLE_PROFESOR'),
            'esAdmin' => $this->isGranted('ROLE_ADMIN'),
            'esEstudiante' => $this->isGranted('ROLE_ESTUDIANTE'),
        ]);
    }

    // Actualiza el nombre, email y opcionalmente la foto de perfil del usuario
    // Valida el formato del email, comprueba que no esté en uso por otra cuenta
    // y verifica el tipo MIME y el estado de la imagen antes de guardarla
    #[Route('/ajustes/actualizar-perfil', name: 'app_actualizar_perfil', methods: ['POST'])]
    public function actualizarPerfil(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $nombre = trim((string) $request->request->get('nombre_usuario'));
        $correo = trim((string) $request->request->get('correo'));

        if ($nombre === '' || $correo === '') {
            $this->addFlash('error', 'El nombre y el correo son obligatorios.');
            return $this->redirectToRoute('app_ajustes');
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'El correo no tiene un formato válido.');
            return $this->redirectToRoute('app_ajustes');
        }

        $usuarioExistente = $userRepository->findOneBy(['email' => $correo]);
        if ($usuarioExistente && $usuarioExistente->getId() !== $usuario->getId()) {
            $this->addFlash('error', 'Ese correo ya está en uso por otra cuenta.');
            return $this->redirectToRoute('app_ajustes');
        }

        $usuario->setNombre($nombre);
        $usuario->setEmail($correo);

        $fotoPerfil = $request->files->get('foto_perfil');

        if ($fotoPerfil instanceof UploadedFile) {
            if (!$fotoPerfil->isValid()) {
                $codigoError = $fotoPerfil->getError();

                $mensaje = match ($codigoError) {
                    UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'La imagen supera el tamaño permitido.',
                    UPLOAD_ERR_PARTIAL => 'La imagen se subió de forma incompleta.',
                    UPLOAD_ERR_NO_FILE => 'No se recibió ninguna imagen.',
                    default => 'La imagen no se pudo procesar correctamente.',
                };

                $this->addFlash('error', $mensaje);
                return $this->redirectToRoute('app_ajustes');
            }

            $mimePermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $mime = (string) $fotoPerfil->getMimeType();

            if (!in_array($mime, $mimePermitidos, true)) {
                $this->addFlash('error', 'Solo se permiten imágenes JPG, PNG, WEBP o GIF.');
                return $this->redirectToRoute('app_ajustes');
            }

            $directorioDestino = $this->getParameter('kernel.project_dir') . '/public/uploads/perfiles';

            if (!is_dir($directorioDestino)) {
                if (!mkdir($directorioDestino, 0775, true) && !is_dir($directorioDestino)) {
                    $this->addFlash('error', 'No se pudo crear la carpeta de imágenes de perfil.');
                    return $this->redirectToRoute('app_ajustes');
                }
            }

            if (!is_writable($directorioDestino)) {
                $this->addFlash('error', 'La carpeta de imágenes no tiene permisos de escritura.');
                return $this->redirectToRoute('app_ajustes');
            }

            $extension = $fotoPerfil->guessExtension() ?: 'jpg';
            $nombreArchivo = uniqid('perfil_', true) . '.' . $extension;

            try {
                $fotoPerfil->move($directorioDestino, $nombreArchivo);
                $usuario->setFotoPerfil($nombreArchivo);
            } catch (FileException $e) {
                $this->addFlash('error', 'No se pudo guardar la imagen subida.');
                return $this->redirectToRoute('app_ajustes');
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Perfil actualizado correctamente.');
        return $this->redirectToRoute('app_ajustes');
    }

    // Tramita la solicitud de un estudiante para obtener una cuenta de profesor
    // El usuario debe elegir un plan y confirmar que ha realizado el pago correspondiente
    // La solicitud queda pendiente de aprobación por parte del administrador
    #[Route('/ajustes/solicitar-cuenta-profesor', name: 'app_ajustes_solicitar_cuenta_profesor', methods: ['POST'])]
    public function solicitarCuentaProfesor(
        Request $request,
        SuscripcionProfesorRepository $suscripcionProfesorRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Un administrador no puede usar esta solicitud.');
            return $this->redirectToRoute('app_ajustes');
        }

        if ($this->isGranted('ROLE_PROFESOR')) {
            $this->addFlash('error', 'Tu cuenta ya dispone de acceso docente.');
            return $this->redirectToRoute('app_ajustes');
        }

        if (!$this->isCsrfTokenValid('solicitar_cuenta_profesor', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $plan = trim((string) $request->request->get('plan'));
        $confirmacionPago = $request->request->getBoolean('confirmacion_pago');

        if (!in_array($plan, ['basico', 'pro', 'premium'], true)) {
            $this->addFlash('error', 'Debes seleccionar un plan válido.');
            return $this->redirectToRoute('app_ajustes');
        }

        if (!$confirmacionPago) {
            $this->addFlash('error', 'Debes confirmar que has revisado la cuenta bancaria y realizado el pago.');
            return $this->redirectToRoute('app_ajustes');
        }

        $limite = match ($plan) {
            'basico' => 1,
            'pro' => 5,
            'premium' => 999,
            default => 1,
        };

        $suscripcionProfesor = $suscripcionProfesorRepository->buscarDeProfesor($usuario);

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

        $this->addFlash('success', 'Solicitud de cuenta de profesor enviada. Queda pendiente de validación por el administrador.');

        return $this->redirectToRoute('app_ajustes');
    }

    // Permite al usuario cambiar su contraseña actual
    // Verifica que la contraseña actual sea correcta, que la nueva tenga al menos
    // 8 caracteres y que ambas repeticiones coincidan antes de actualizar
    #[Route('/ajustes/cambiar-contrasena', name: 'app_cambiar_contrasena', methods: ['POST'])]
    public function cambiarContrasena(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $contrasenaActual = (string) $request->request->get('contrasena_actual');
        $nuevaContrasena = (string) $request->request->get('nueva_contrasena');
        $repetirNuevaContrasena = (string) $request->request->get('repetir_nueva_contrasena');

        if ($contrasenaActual === '' || $nuevaContrasena === '' || $repetirNuevaContrasena === '') {
            $this->addFlash('error', 'Debes completar todos los campos de contraseña.');
            return $this->redirectToRoute('app_ajustes');
        }

        if (!$passwordHasher->isPasswordValid($usuario, $contrasenaActual)) {
            $this->addFlash('error', 'La contraseña actual no es correcta.');
            return $this->redirectToRoute('app_ajustes');
        }

        if ($nuevaContrasena !== $repetirNuevaContrasena) {
            $this->addFlash('error', 'La nueva contraseña y su repetición no coinciden.');
            return $this->redirectToRoute('app_ajustes');
        }

        if (mb_strlen($nuevaContrasena) < 8) {
            $this->addFlash('error', 'La nueva contraseña debe tener al menos 8 caracteres.');
            return $this->redirectToRoute('app_ajustes');
        }

        $usuario->setPassword($passwordHasher->hashPassword($usuario, $nuevaContrasena));
        $entityManager->flush();

        $this->addFlash('success', 'Contraseña actualizada correctamente.');

        return $this->redirectToRoute('app_ajustes');
    }
}