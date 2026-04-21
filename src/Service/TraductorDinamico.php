<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TraductorDinamico
{
    private const CACHE_VERSION = 'v6_no_cache_on_failure';
    private const API_URL = 'http://127.0.0.1:5000/translate';
    private const PROTECTED_TAGS = ['script', 'style', 'textarea'];
    private const PLACEHOLDER_ATTR = 'data-traductor-placeholder';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheItemPoolInterface $cache
    ) {
    }

    public function traducir(?string $texto, string $destino = 'es', string $origen = 'es', string $format = 'text'): string
    {
        if ($texto === null || trim($texto) === '') {
            return '';
        }

        if ($destino === $origen) {
            return $texto;
        }

        $cacheKey = 'trad_' . $format . '_' . md5(self::CACHE_VERSION . '_' . $origen . '_' . $destino . '_' . $texto);
        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return (string) $item->get();
        }

        try {
            $response = $this->httpClient->request('POST', self::API_URL, [
                'json' => [
                    'q' => $texto,
                    'source' => $origen,
                    'target' => $destino,
                    'format' => $format,
                ],
                'timeout' => 20,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 300) {
                return $texto;
            }

            $data = $response->toArray(false);
            $translated = $data['translatedText'] ?? $texto;

            if (!is_string($translated) || trim($translated) === '') {
                return $texto;
            }

            // Solo cachear traducciones válidas
            $item->set($translated);
            $item->expiresAfter(604800);
            $this->cache->save($item);

            return $translated;
        } catch (\Throwable) {
            // Si falla la API, devolver original PERO NO cachearlo
            return $texto;
        }
    }

    public function traducirContenidoRenderizado(string $html, string $destino = 'es', string $origen = 'es'): string
    {
        if (trim($html) === '' || $destino === $origen) {
            return $html;
        }

        $cacheKey = 'trad_render_main_' . md5(self::CACHE_VERSION . '_' . $origen . '_' . $destino . '_' . $html);
        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return (string) $item->get();
        }

        $resultado = $this->traducirBloquesDinamicos($html, $destino, $origen);

        // Solo cachear si realmente cambió algo
        if ($resultado !== $html) {
            $item->set($resultado);
            $item->expiresAfter(604800);
            $this->cache->save($item);
        }

        return $resultado;
    }

    private function traducirBloquesDinamicos(string $html, string $destino, string $origen): string
    {
        $offset = 0;

        while (preg_match('/<([a-z0-9:-]+)\b[^>]*\bdata-dynamic-translate=("|\')1\2[^>]*>/i', $html, $matches, PREG_OFFSET_CAPTURE, $offset) === 1) {
            $tag = strtolower($matches[1][0]);
            $apertura = $matches[0][0];
            $inicio = $matches[0][1];
            $bloque = $this->extraerBloqueBalanceado($html, $tag, $inicio, $apertura);

            if ($bloque === null) {
                $offset = $inicio + strlen($apertura);
                continue;
            }

            $bloqueTraducido = $this->traducirBloqueDinamico($bloque, $destino, $origen);
            $html = substr($html, 0, $inicio) . $bloqueTraducido . substr($html, $inicio + strlen($bloque));
            $offset = $inicio + strlen($bloqueTraducido);
        }

        return $html;
    }

    private function traducirBloqueDinamico(string $bloque, string $destino, string $origen): string
    {
        if (preg_match('/^(<([a-z0-9:-]+)\b[^>]*>)(.*)(<\/\2>)$/si', $bloque, $matches) !== 1) {
            return $bloque;
        }

        $apertura = $matches[1];
        $contenido = $matches[3];
        $cierre = $matches[4];

        $bloquesProtegidos = [];
        $contenido = $this->protegerBloquesNoTraducibles($contenido, $bloquesProtegidos);
        $contenido = $this->protegerEtiquetasEspeciales($contenido, $bloquesProtegidos);

        $contenidoTraducido = $this->traducir($contenido, $destino, $origen, 'html');

        if (!is_string($contenidoTraducido) || trim($contenidoTraducido) === '') {
            $contenidoTraducido = $contenido;
        }

        $contenidoTraducido = $this->restaurarBloquesProtegidos($contenidoTraducido, $bloquesProtegidos);

        return $apertura . $contenidoTraducido . $cierre;
    }

    private function protegerBloquesNoTraducibles(string $html, array &$bloquesProtegidos): string
    {
        $offset = 0;

        while (preg_match('/<([a-z0-9:-]+)\b[^>]*\bdata-no-translate=("|\')1\2[^>]*>/i', $html, $matches, PREG_OFFSET_CAPTURE, $offset) === 1) {
            $tag = strtolower($matches[1][0]);
            $apertura = $matches[0][0];
            $inicio = $matches[0][1];
            $bloque = $this->extraerBloqueBalanceado($html, $tag, $inicio, $apertura);

            if ($bloque === null) {
                $offset = $inicio + strlen($apertura);
                continue;
            }

            $token = 'bloque_no_trad_' . count($bloquesProtegidos);
            $bloquesProtegidos[$token] = $bloque;

            $placeholder = '<span ' . self::PLACEHOLDER_ATTR . '="' . $token . '"></span>';
            $html = substr($html, 0, $inicio) . $placeholder . substr($html, $inicio + strlen($bloque));
            $offset = $inicio + strlen($placeholder);
        }

        return $html;
    }

    private function protegerEtiquetasEspeciales(string $html, array &$bloquesProtegidos): string
    {
        foreach (self::PROTECTED_TAGS as $tag) {
            $html = preg_replace_callback(
                '/<' . $tag . '\b[^>]*>.*?<\/' . $tag . '>/si',
                function (array $matches) use (&$bloquesProtegidos): string {
                    $token = 'bloque_protegido_' . count($bloquesProtegidos);
                    $bloquesProtegidos[$token] = $matches[0];

                    return '<span ' . self::PLACEHOLDER_ATTR . '="' . $token . '"></span>';
                },
                $html
            ) ?? $html;
        }

        return $html;
    }

    private function extraerBloqueBalanceado(string $html, string $tag, int $inicio, string $apertura): ?string
    {
        if (preg_match('/\/>\s*$/', $apertura) === 1) {
            return $apertura;
        }

        $patron = '/<\/?' . preg_quote($tag, '/') . '\b[^>]*>/i';
        $offset = $inicio;
        $profundidad = 0;
        $fin = null;

        while (preg_match($patron, $html, $coincidencia, PREG_OFFSET_CAPTURE, $offset) === 1) {
            $etiqueta = $coincidencia[0][0];
            $posicion = $coincidencia[0][1];
            $esCierre = str_starts_with($etiqueta, '</');
            $esAutocierre = !$esCierre && preg_match('/\/>\s*$/', $etiqueta) === 1;

            if (!$esCierre && !$esAutocierre) {
                $profundidad++;
            }

            if ($esCierre) {
                $profundidad--;
                if ($profundidad === 0) {
                    $fin = $posicion + strlen($etiqueta);
                    break;
                }
            }

            $offset = $posicion + strlen($etiqueta);
        }

        if ($fin === null) {
            return null;
        }

        return substr($html, $inicio, $fin - $inicio);
    }

    private function restaurarBloquesProtegidos(string $html, array $bloquesProtegidos): string
    {
        if ($bloquesProtegidos === []) {
            return $html;
        }

        return preg_replace_callback(
            '/<span\s+' . self::PLACEHOLDER_ATTR . '="([^"]+)"><\/span>/i',
            static function (array $matches) use ($bloquesProtegidos): string {
                $token = $matches[1];

                return $bloquesProtegidos[$token] ?? $matches[0];
            },
            $html
        ) ?? $html;
    }
}