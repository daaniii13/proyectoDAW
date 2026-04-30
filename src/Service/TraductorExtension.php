<?php

namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

// Extensión de Twig para poder usar el filtro traducir_dinamico
class TraductorExtension extends AbstractExtension {
    public function __construct(
        private readonly TraductorDinamico $traductorDinamico,
        private readonly RequestStack $requestStack
    ) {
    }

    // Registra los filtros personalizados disponibles en Twig
    public function getFilters(): array {
        return [
            new TwigFilter('traducir_dinamico', [$this, 'traducirDinamico']),
        ];
    }

    // Traduce un texto dinámicamente según el idioma actual
    public function traducirDinamico(?string $texto): string {
        // Si no hay texto, devuelve vacío
        if ($texto === null || trim($texto) === '') {
            return '';
        }

        // Obtiene el idioma actual de la petición
        $request = $this->requestStack->getCurrentRequest();
        $locale = $request?->getLocale() ?? 'es';

        // Si el idioma es español, no hace falta traducir
        if ($locale === 'es') {
            return $texto;
        }

        // Traduce desde español al idioma actual.
        return $this->traductorDinamico->traducir($texto, $locale, 'es');
    }
}
