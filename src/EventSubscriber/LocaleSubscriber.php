<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

// Suscriptor de eventos que aplica el idioma del usuario en cada petición HTTP
// Lee el idioma almacenado en sesión por IdiomaController y lo asigna al objeto
// Request para que Symfony y Twig lo usen durante toda la petición
class LocaleSubscriber implements EventSubscriberInterface
{
    // Si no hay idioma en sesión aplica el idioma por defecto configurado ('es')
    public function __construct(
        private readonly string $defaultLocale = 'es'
    ) {
    }

    // Se ejecuta en cada petición HTTP antes de que Symfony resuelva el controlador
    public function onKernelRequest(RequestEvent $event): void
    {
        // Ignora las subpeticiones internas de Symfony
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Accede a la sesión solo si existe
        $session = $request->hasSession() ? $request->getSession() : null;

        // Si la sesión tiene un idioma guardado lo aplica al Request
        if ($session && $session->has('_locale')) {
            $request->setLocale((string) $session->get('_locale'));
            return;
        }

        // Sin idioma en sesión aplica el idioma por defecto
        $request->setLocale($this->defaultLocale);
    }

    // Declara a qué eventos se suscribe esta clase y con qué prioridad
    // La prioridad 20 garantiza que el idioma se aplica antes de que se resuelva el controlador
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }
}