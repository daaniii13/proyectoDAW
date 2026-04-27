<?php

namespace App\EventSubscriber;

use App\Service\TraductorDinamico;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

// Suscriptor de eventos que traduce el HTML generado por Twig antes de enviarlo al navegador
// Intercepta la respuesta HTTP cuando el idioma activo no es el español (idioma base) y
// delega la traducción al servicio TraductorDinamico, que procesa el HTML completo
class TraductorRespuestaSubscriber implements EventSubscriberInterface
{
    // Recibe el servicio TraductorDinamico
    public function __construct(
        private readonly TraductorDinamico $traductorDinamico
    ) {
    }

    // Se ejecuta justo antes de que Symfony envíe la respuesta HTTP al navegador
    // Aplica múltiples filtros para determinar si la respuesta debe traducirse
    // Si todos los filtros se cumplen, reemplaza el contenido de la respuesta con el HTML traducido por TraductorDinamico
    public function onKernelResponse(ResponseEvent $event): void
    {
        // Ignora las subpeticiones internas de Symfony
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->isXmlHttpRequest()) {
            return;
        }

        // Excluye rutas internas que no forman parte de la interfaz de usuario
        $path = $request->getPathInfo();
        if (
            str_starts_with($path, '/_wdt') || 
            str_starts_with($path, '/_profiler') || 
            str_starts_with($path, '/build') || 
            str_starts_with($path, '/uploads')      
        ) {
            return;
        }

        // Solo traduce respuestas de tipo HTML
        $contentType = (string) $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            return;
        }

        // Si el idioma activo es español o está vacío, no hay nada que traducir
        $locale = (string) $request->getLocale();
        if ($locale === '' || $locale === 'es') {
            return;
        }

        $html = $response->getContent();

        // Se asegura de que el contenido sea una cadena no vacía antes de procesarla
        if (!is_string($html) || trim($html) === '') {
            return;
        }

        // traducirContenidoRenderizado() recibe el HTML, el idioma destino y el idioma origen
        $response->setContent(
            $this->traductorDinamico->traducirContenidoRenderizado($html, $locale, 'es')
        );
    }

    // Declara a qué eventos se suscribe esta clase y con qué prioridad
    // La prioridad -10 garantiza que actua al final del ciclo de respuesta
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -10],
        ];
    }
}