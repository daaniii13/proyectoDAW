<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;


// Controlador de autenticación con Google mediante OAuth2
class GoogleAuthController extends AbstractController
{
    // Inicia el flujo OAuth2 con Google
    // Redirige al usuario a la página de login de Google solicitando acceso al email y al perfil básico del usuario
    #[Route('/connect/google', name: 'app_google_start')]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], []);
    }

    // Punto de retorno al que Google redirige al usuario tras autenticarse
    #[Route('/connect/google/check', name: 'app_google_check')]
    public function connectCheck(): void
    {
        throw new \LogicException('Esta ruta la gestiona el autenticador de Google.');
    }
}
