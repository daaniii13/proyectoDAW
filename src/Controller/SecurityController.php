<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// SecurityController gestiona el formulario de login y el cierre de sesión
class SecurityController extends AbstractController
{
    // Muestra el formulario de login. Si el usuario ya tiene sesión activa,
    // lo redirige directamente a la página de inicio para evitar mostrarle el formulario de nuevo.
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_inicio');
        }

        return $this->render('paginas/autenticacion/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    // Esta ruta no ejecuta código real. Symfony la intercepta antes de llegar aquí
    // gracias a la configuración del firewall en security.yaml.
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Interceptado por Symfony.');
    }
}