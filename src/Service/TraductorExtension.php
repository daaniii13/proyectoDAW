<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TraductorExtension extends AbstractExtension
{
    public function __construct(
        private readonly TraductorDinamico $traductorDinamico,
        private readonly RequestStack $requestStack
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('traducir_dinamico', [$this, 'traducirDinamico']),
        ];
    }

    public function traducirDinamico(?string $texto): string
    {
        if ($texto === null || trim($texto) === '') {
            return '';
        }

        $request = $this->requestStack->getCurrentRequest();
        $locale = $request?->getLocale() ?? 'es';

        if ($locale === 'es') {
            return $texto;
        }

        return $this->traductorDinamico->traducir($texto, $locale, 'es');
    }
}