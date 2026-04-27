<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de cambio de idioma de la interfaz
// Almacena el idioma elegido por el usuario en la sesión con la clave '_locale',
// que el LocaleSubscriber lee en cada petición para aplicarlo globalmente
class IdiomaController extends AbstractController
{
    // Cambia el idioma activo de la sesión y redirige al usuario a la página anterior
    // Si no hay cabecera Referer disponible, redirige a la página de inicio
    #[Route('/idioma/{locale}', name: 'app_cambiar_idioma', methods: ['GET'])]
    public function cambiar(string $locale, Request $request): RedirectResponse
    {
        $idiomasPermitidos = ['es', 'en'];

        if (!in_array($locale, $idiomasPermitidos, true)) {
            $locale = 'es';
        }

        // Guardamos el idioma en sesión para que el LocaleSubscriber lo aplique en las siguientes peticiones
        $request->getSession()->set('_locale', $locale);

        $referer = $request->headers->get('referer');

        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_inicio');
    }
}
