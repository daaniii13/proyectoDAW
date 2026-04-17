<?php

namespace App\Controller;

use App\Entity\RecuperacionContrasena;
use App\Repository\RecuperacionContrasenaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// Controlador del flujo de recuperación de contraseña en dos pasos:
// El primer paso permite al usuario solicitar un enlace de recuperación por email
// El segundo paso procesa el formulario de nueva contraseña una vez el usuario accede al enlace recibido en su correo
class RecuperacionController extends AbstractController
{
    // Muestra el formulario de solicitud de recuperación y genera un token único de 64 caracteres, invalida los  
    // anteriores pendientes y envía al usuario un email con el enlace de restablecimiento que caduca en 1 hora
    #[Route('/recuperar-contrasena', name: 'app_recuperar_contrasena', methods: ['GET', 'POST'])]
    public function solicitar(
        Request $request,
        UserRepository $userRepository,
        RecuperacionContrasenaRepository $recuperacionRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $error = null;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('recuperar_contrasena', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF no válido.');
            }

            $correo = trim((string) $request->request->get('correo_recuperacion'));

            if ($correo === '') {
                $error = 'Debes introducir tu correo electrónico.';
            } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $error = 'El correo electrónico no es válido.';
            } else {
                $usuario = $userRepository->findOneBy(['email' => $correo]);

                if (!$usuario) {
                    $error = 'No existe ninguna cuenta con ese correo electrónico.';
                } else {
                    // Invalida los tokens anteriores no usados para que solo el nuevo sea válido
                    $anteriores = $recuperacionRepository->findBy([
                        'usuario' => $usuario,
                        'usado' => false,
                    ]);

                    foreach ($anteriores as $anterior) {
                        $anterior->setUsado(true);
                    }

                    // Genera un token seguro de 64 caracteres hexadecimales
                    $token = bin2hex(random_bytes(32));

                    $recuperacion = new RecuperacionContrasena();
                    $recuperacion->setUsuario($usuario);
                    $recuperacion->setToken($token);
                    $recuperacion->setFechaExpiracion(new \DateTimeImmutable('+1 hour'));
                    $recuperacion->setUsado(false);

                    $entityManager->persist($recuperacion);
                    $entityManager->flush();

                    // Genera la URL absoluta del enlace (necesaria para que funcione desde el correo)
                    $urlAbsoluta = $this->generateUrl(
                        'app_restablecer_contrasena',
                        ['token' => $token],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    try {
                        $nombreUsuario = (string) $usuario->getNombre();

                        $email = (new Email())
                            ->from('teachingexplorerdaw@gmail.com')
                            ->to((string) $usuario->getEmail())
                            ->subject('Restablecer contraseña - TeachingExplorer')
                            ->text(
                                "Hola {$nombreUsuario},\n\n" .
                                "Has solicitado restablecer tu contraseña.\n\n" .
                                "Pulsa este enlace para continuar:\n{$urlAbsoluta}\n\n" .
                                "Este enlace caduca en 1 hora."
                            )
                            ->html(
                                '<h2>Restablecer contraseña</h2>' .
                                '<p>Hola ' . htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') . ',</p>' .
                                '<p>Has solicitado restablecer tu contraseña.</p>' .
                                '<p><a href="' . htmlspecialchars($urlAbsoluta, ENT_QUOTES, 'UTF-8') . '">Pulsa aquí para cambiarla</a></p>' .
                                '<p>Este enlace caduca en 1 hora.</p>'
                            );

                        $mailer->send($email);

                        $this->addFlash('success', 'Te hemos enviado un enlace de recuperación a tu correo.');

                        return $this->redirectToRoute('app_login');
                    } catch (\Throwable $e) {
                        $error = 'No se pudo enviar el correo de recuperación: ' . $e->getMessage();
                    }
                }
            }
        }

        return $this->render('paginas/autenticacion/recuperar_contrasenia.html.twig', [
            'error' => $error,
        ]);
    }

    // Verifica que el token recibido sea válido, muestra el formulario de nueva contraseña,
    // actualiza la contraseña del usuario y marca el token como usado para que no pueda reutilizarse
    #[Route('/restablecer-contrasena/{token}', name: 'app_restablecer_contrasena', methods: ['GET', 'POST'])]
    public function restablecer(
        string $token,
        Request $request,
        RecuperacionContrasenaRepository $recuperacionRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $recuperacion = $recuperacionRepository->buscarTokenValido($token);

        if (!$recuperacion) {
            throw $this->createNotFoundException('El enlace de recuperación no es válido o ha caducado.');
        }

        $error = null;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('restablecer_contrasena', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF no válido.');
            }

            $nuevaContrasena        = (string) $request->request->get('nueva_contrasena');
            $repetirNuevaContrasena = (string) $request->request->get('repetir_nueva_contrasena');

            if ($nuevaContrasena === '' || $repetirNuevaContrasena === '') {
                $error = 'Todos los campos son obligatorios.';
            } elseif ($nuevaContrasena !== $repetirNuevaContrasena) {
                $error = 'Las contraseñas no coinciden.';
            } elseif (mb_strlen($nuevaContrasena) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (
                !preg_match('/[A-Z]/', $nuevaContrasena) ||
                !preg_match('/[a-z]/', $nuevaContrasena) ||
                !preg_match('/\d/', $nuevaContrasena)
            ) {
                $error = 'La contraseña debe incluir al menos una mayúscula, una minúscula y un número.';
            } else {
                $usuario = $recuperacion->getUsuario();

                $usuario->setPassword(
                    $passwordHasher->hashPassword($usuario, $nuevaContrasena)
                );

                // Invalida el token para que no pueda usarse de nuevo
                $recuperacion->setUsado(true);

                $entityManager->flush();

                $this->addFlash('success', 'Tu contraseña se ha restablecido correctamente.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('paginas/autenticacion/restablecer_contrasenia.html.twig', [
            'error' => $error,
            'token' => $token,
        ]);
    }
}