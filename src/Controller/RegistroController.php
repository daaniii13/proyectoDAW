<?php

namespace App\Controller;

use App\Entity\SuscripcionProfesor;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

// Controlador del proceso de registro de nuevos usuarios.
// Si el usuario elige el rol docente, debe seleccionar un plan y confirmar el pago,
// tras lo cual se crea una solicitud de alta pendiente de aprobación por el administrador
class RegistroController extends AbstractController
{
    // Muestra el formulario de registro y valida el formato del email, la complejidad de la contraseña,
    // la unicidad del email en el sistema, y los requisitos adicionales para el rol de profesor
    // Redirige al usuario ya autenticado directamente al inicio
    #[Route('/registro', name: 'app_registro', methods: ['GET', 'POST'])]
    public function registro(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Si el usuario ya tiene sesión activa, no necesita registrarse de nuevo
        if ($this->getUser()) {
            return $this->redirectToRoute('app_inicio');
        }

        $error = null;
        $datos = [
            'nombre' => '',
            'correo' => '',
            'tipo_usuario' => '',
            'plan_profesor' => '',
            'confirmacion_pago' => false,
        ];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('registro_usuario', (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF no válido.');
            }

            $nombre = trim((string) $request->request->get('nombre'));
            $email = trim((string) $request->request->get('correo'));
            $tipoUsuario = trim((string) $request->request->get('tipo_usuario'));
            $planProfesor = trim((string) $request->request->get('plan_profesor'));
            $password = (string) $request->request->get('contrasena');
            $repetirPassword = (string) $request->request->get('repetir_contrasena');
            $confirmacionPago = $request->request->getBoolean('confirmacion_pago');

            // Guarda los datos introducidos para repoblar el formulario en caso de error
            $datos = [
                'nombre' => $nombre,
                'correo' => $email,
                'tipo_usuario' => $tipoUsuario,
                'plan_profesor' => $planProfesor,
                'confirmacion_pago' => $confirmacionPago,
            ];

            if ($nombre === '' || $email === '' || $tipoUsuario === '' || $password === '' || $repetirPassword === '') {
                $error = 'Todos los campos son obligatorios.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El correo electrónico no es válido.';
            } elseif (!in_array($tipoUsuario, ['estudiante', 'profesor'], true)) {
                $error = 'El tipo de cuenta no es válido.';
            } elseif ($tipoUsuario === 'profesor' && !in_array($planProfesor, ['basico', 'pro', 'premium'], true)) {
                $error = 'Debes elegir un plan de profesor válido.';
            } elseif ($tipoUsuario === 'profesor' && !$confirmacionPago) {
                $error = 'Debes confirmar que has revisado la cuenta bancaria y realizado el pago del plan docente.';
            } elseif ($password !== $repetirPassword) {
                $error = 'Las contraseñas no coinciden.';
            } elseif (mb_strlen($password) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (
                !preg_match('/[A-Z]/', $password) ||
                !preg_match('/[a-z]/', $password) ||
                !preg_match('/\d/', $password)
            ) {
                $error = 'La contraseña debe incluir al menos una mayúscula, una minúscula y un número.';
            } elseif ($userRepository->findOneBy(['email' => $email])) {
                $error = 'Ya existe una cuenta con ese correo electrónico.';
            } else {
                // Todos los datos son válidos: Crea el usuario con rol de estudiante por defecto
                $user = new User();
                $user->setNombre($nombre);
                $user->setEmail($email);
                $user->setRoles(['ROLE_ESTUDIANTE']);
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $password)
                );

                $entityManager->persist($user);
                $entityManager->flush();

                // Si eligió el rol de profesor, crea la solicitud de suscripción pendiente
                if ($tipoUsuario === 'profesor') {
                    $limite = match ($planProfesor) {
                        'basico' => 1,
                        'pro' => 5,
                        'premium' => 999,
                        default => 1,
                    };

                    $suscripcion = new SuscripcionProfesor();
                    $suscripcion->setProfesor($user);
                    $suscripcion->setPlan($planProfesor);
                    $suscripcion->setPlanSolicitado($planProfesor);
                    $suscripcion->setTipoSolicitud('alta');
                    $suscripcion->setEstado('pendiente');
                    $suscripcion->setLimiteCursos($limite);
                    $suscripcion->setFechaSolicitud(new \DateTimeImmutable());

                    $entityManager->persist($suscripcion);
                    $entityManager->flush();

                    $this->addFlash('success', 'Cuenta creada. Tu solicitud docente y el plan seleccionado han sido enviados al administrador para validación.');
                } else {
                    $this->addFlash('success', 'Cuenta creada correctamente. Ya puedes iniciar sesión.');
                }

                $redirectUrl = $request->request->get('redirect') ?? $request->query->get('redirect');

                return $this->redirectToRoute('app_login', [
                    'redirect' => ($redirectUrl && str_starts_with($redirectUrl, '/')) ? $redirectUrl : null
                ]);
            }
        }

        // Lee la URL de retorno desde el parámetro GET de la petición
        $redirectUrl = $request->query->get('redirect');

        return $this->render('paginas/autenticacion/registro.html.twig', [
            'error' => $error,
            'datos' => $datos,
            'cuentaBancariaDocente' => 'ES12 3456 7890 1234 5678 9012',
            // Pasa la URL de retorno al template para incluirla como campo oculto en el formulario
            'redirectUrl' => $redirectUrl,
        ]);
    }
}

