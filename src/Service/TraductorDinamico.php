<?php

namespace App\Service;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// Servicio para traducir textos y bloques HTML de forma dinámica
class TraductorDinamico {
    private const CACHE_VERSION = 'v6_no_cache_on_failure';
    private const API_URL = 'https://translate.fedilab.app/translate';
    // private const API_URL = 'http://127.0.0.1:5000/translate'; SOLO SE APLICARÍA SI DA ERROR LA API Y SE NECESITA EJECUTAR LA API EN LOCAL

    // Etiquetas que no deben traducirse
    private const PROTECTED_TAGS = ['script', 'style', 'textarea'];

    // Atributo usado para marcar placeholders temporales
    private const PLACEHOLDER_ATTR = 'data-traductor-placeholder';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheItemPoolInterface $cache
    ) {
    }

    // Traduce un texto normal o HTML usando la API externa
    public function traducir(?string $texto, string $destino = 'es', string $origen = 'es', string $format = 'text'): string {
        if ($texto === null || trim($texto) === '') {
            return '';
        }

        // Si origen y destino son iguales, no se traduce
        if ($destino === $origen) {
            return $texto;
        }

        // Clave única para guardar la traducción en caché
        $cacheKey = 'trad_' . $format . '_' . md5(self::CACHE_VERSION . '_' . $origen . '_' . $destino . '_' . $texto);
        $item = $this->cache->getItem($cacheKey);

        // Si ya existe en caché, se devuelve directamente
        if ($item->isHit()) {
            return (string) $item->get();
        }

        try {
            // Petición a la API de traducción
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

            // Si la API falla, se devuelve el texto original
            if ($statusCode < 200 || $statusCode >= 300) {
                return $texto;
            }

            $data = $response->toArray(false);
            $translated = $data['translatedText'] ?? $texto;

            // Evita guardar respuestas vacías o inválidas
            if (!is_string($translated) || trim($translated) === '') {
                return $texto;
            }

            // Guarda solo traducciones correctas durante 7 días
            $item->set($translated);
            $item->expiresAfter(604800);
            $this->cache->save($item);

            return $translated;
        } catch (\Throwable) {
            // Si hay error, devuelve el original y no lo cachea
            return $texto;
        }
    }

    // Traduce solo los bloques HTML marcados con data-dynamic-translate="1"
    public function traducirContenidoRenderizado(string $html, string $destino = 'es', string $origen = 'es'): string  {
        if (trim($html) === '' || $destino === $origen) {
            return $html;
        }

        $cacheKey = 'trad_render_main_' . md5(self::CACHE_VERSION . '_' . $origen . '_' . $destino . '_' . $html);
        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            return (string) $item->get();
        }

        $resultado = $this->traducirBloquesDinamicos($html, $destino, $origen);

        // Cachea solo si hubo algún cambio real.
        if ($resultado !== $html) {
            $item->set($resultado);
            $item->expiresAfter(604800);
            $this->cache->save($item);
        }

        return $resultado;
    }

    // Busca bloques marcados para traducir dentro del HTML
    private function traducirBloquesDinamicos(string $html, string $destino, string $origen): string  {
        $offset = 0;

        while (preg_match('/<([a-z0-9:-]+)\b[^>]*\bdata-dynamic-translate=("|\')1\2[^>]*>/i', $html, $matches, PREG_OFFSET_CAPTURE, $offset) === 1) {
            $tag = strtolower($matches[1][0]);
            $apertura = $matches[0][0];
            $inicio = $matches[0][1];

            // Extrae el bloque completo, respetando etiquetas anidadas
            $bloque = $this->extraerBloqueBalanceado($html, $tag, $inicio, $apertura);

            if ($bloque === null) {
                $offset = $inicio + strlen($apertura);
                continue;
            }

            // Traduce el bloque y lo sustituye en el HTML
            $bloqueTraducido = $this->traducirBloqueDinamico($bloque, $destino, $origen);
            $html = substr($html, 0, $inicio) . $bloqueTraducido . substr($html, $inicio + strlen($bloque));
            $offset = $inicio + strlen($bloqueTraducido);
        }

        return $html;
    }

    // Traduce el contenido interno de un bloque HTML
    private function traducirBloqueDinamico(string $bloque, string $destino, string $origen): string  {
        if (preg_match('/^(<([a-z0-9:-]+)\b[^>]*>)(.*)(<\/\2>)$/si', $bloque, $matches) !== 1) {
            return $bloque;
        }

        $apertura = $matches[1];
        $contenido = $matches[3];
        $cierre = $matches[4];

        $bloquesProtegidos = [];

        // Protege partes que no deben traducirse
        $contenido = $this->protegerBloquesNoTraducibles($contenido, $bloquesProtegidos);
        $contenido = $this->protegerEtiquetasEspeciales($contenido, $bloquesProtegidos);

        // Traduce el contenido como HTML
        $contenidoTraducido = $this->traducir($contenido, $destino, $origen, 'html');

        if (!is_string($contenidoTraducido) || trim($contenidoTraducido) === '') {
            $contenidoTraducido = $contenido;
        }

        // Recupera las partes protegidas
        $contenidoTraducido = $this->restaurarBloquesProtegidos($contenidoTraducido, $bloquesProtegidos);

        return $apertura . $contenidoTraducido . $cierre;
    }

    // Protege bloques con data-no-translate="1"
    private function protegerBloquesNoTraducibles(string $html, array &$bloquesProtegidos): string  {
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

            // Guarda el bloque original y pone un placeholder
            $token = 'bloque_no_trad_' . count($bloquesProtegidos);
            $bloquesProtegidos[$token] = $bloque;

            $placeholder = '<span ' . self::PLACEHOLDER_ATTR . '="' . $token . '"></span>';
            $html = substr($html, 0, $inicio) . $placeholder . substr($html, $inicio + strlen($bloque));
            $offset = $inicio + strlen($placeholder);
        }

        return $html;
    }

    // Protege etiquetas como script, style y textarea
    private function protegerEtiquetasEspeciales(string $html, array &$bloquesProtegidos): string {
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

    // Extrae una etiqueta completa, incluso si tiene etiquetas iguales dentro
    private function extraerBloqueBalanceado(string $html, string $tag, int $inicio, string $apertura): ?string {
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

    // Sustituye los placeholders por sus bloques originales
    private function restaurarBloquesProtegidos(string $html, array $bloquesProtegidos): string {
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
