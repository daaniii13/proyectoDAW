<?php

namespace App\Controller;

use App\Repository\CursoRepository;
use App\Service\TraductorDinamico;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

class ApiCursoController extends AbstractController
{
    #[Route('/api/cursos', name: 'api_cursos')]
    public function listar(
        Request $request,
        CursoRepository $repo,
        TraductorDinamico $traductorDinamico
    ): JsonResponse {
        $busqueda = trim((string) $request->query->get('q', ''));
        $session = $request->getSession();
        $locale = strtolower((string) $request->getLocale());
        $destino = str_starts_with($locale, 'en') ? 'en' : 'es';
        $idiomaCurso = $destino === 'en' ? 'en' : 'es';

        /* Guarda historial de búsquedas recientes */
        $historialBusquedas = $session->get('busquedas_recientes_cursos', $session->get('busquedas', []));
        if ($busqueda !== '') {
            array_unshift($historialBusquedas, $busqueda);
            $historialBusquedas = array_values(array_unique(array_filter($historialBusquedas)));
            $historialBusquedas = array_slice($historialBusquedas, 0, 10);

            /* Mantiene compatibilidad con las dos claves antiguas */
            $session->set('busquedas_recientes_cursos', $historialBusquedas);
            $session->set('busquedas', $historialBusquedas);
        }

        $traducirTexto = function (?string $texto) use ($traductorDinamico, $destino, $idiomaCurso): string {
            if ($texto === null || trim($texto) === '') {
                return '';
            }

            /* Solo traduce automáticamente cuando el curso viene en español y la web está en inglés */
            if ($destino === 'en' && $idiomaCurso === 'es') {
                return $traductorDinamico->traducir($texto, 'en', 'es');
            }

            return $texto;
        };

        $mapearCurso = function ($curso) use ($traducirTexto) {
            return [
                'id' => $curso->getId(),
                'titulo' => $traducirTexto($curso->getTitulo()),
                'descripcion' => $traducirTexto($curso->getDescripcion()),
                'nivel' => $traducirTexto($curso->getNivel()),
                'modalidad' => $traducirTexto($curso->getModalidad()),
                'profesor' => $curso->getProfesor()?->getNombre(),
                'duracion' => $traducirTexto($curso->getDuracion()),
                'precio' => $curso->getPrecio(),
                'idioma' => $curso->getIdioma(),
                'detalleUrl' => $this->generateUrl('app_detalle_curso', ['id' => $curso->getId()]),
            ];
        };

        /* Resultados de búsqueda normales, pero ya filtrados por idioma del curso */
        if ($busqueda !== '') {
            $resultados = $repo->buscarPorTextoEIdioma($busqueda, $idiomaCurso);

            if (!empty($resultados)) {
                $dataResultados = array_map($mapearCurso, $resultados);

                return $this->json([
                    'modo' => 'resultados_busqueda',
                    'mensaje' => '',
                    'cursos' => $dataResultados,
                    'destacados' => [],
                ]);
            }
        }

        /* Recomendador por defecto y fallback sin resultados, siempre dentro del idioma actual */
        $cursos = $repo->buscarCursosActivosPorIdioma($idiomaCurso);
        $data = array_map($mapearCurso, $cursos);

        $idsCursosClicados = $session->get('cursos_clicados_recientes', []);
        if (empty($idsCursosClicados)) {
            $idsCursosClicados = $session->get('clics', []);
        }

        $payload = [
            'cursos' => $data,
            'busqueda_actual' => $busqueda,
            'busquedas_recientes' => $historialBusquedas,
            'ids_cursos_clicados' => $idsCursosClicados,
            'idioma_web' => $idiomaCurso,
        ];

        $tmp = tempnam(sys_get_temp_dir(), 'rec_');
        file_put_contents($tmp, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $script = $this->getParameter('kernel.project_dir') . '/python/recomendador.py';

        $comandos = [
            ['python', $script, $tmp],
            ['python3', $script, $tmp],
            ['py', $script, $tmp],
        ];

        $salida = null;
        $procesoCorrecto = false;

        foreach ($comandos as $comando) {
            $process = new Process($comando);
            $process->run();

            if ($process->isSuccessful()) {
                $salida = json_decode($process->getOutput(), true);
                $procesoCorrecto = is_array($salida);
                if ($procesoCorrecto) {
                    break;
                }
            }
        }

        @unlink($tmp);

        $mensajeSinResultados = $destino === 'en'
            ? 'No results were found for this search.'
            : 'No hay resultados para esta búsqueda.';

        if (!$procesoCorrecto) {
            return $this->json([
                'modo' => $busqueda === '' ? 'recomendados' : 'sin_resultados',
                'cursos' => [],
                'destacados' => array_slice($data, 0, 3),
                'mensaje' => $busqueda === '' ? '' : $mensajeSinResultados,
            ]);
        }

        if (isset($salida['cursos']) && is_array($salida['cursos'])) {
            $salida['cursos'] = array_map(function (array $curso) {
                return [
                    'id' => $curso['id'] ?? null,
                    'titulo' => $curso['titulo'] ?? '',
                    'descripcion' => $curso['descripcion'] ?? '',
                    'nivel' => $curso['nivel'] ?? '',
                    'modalidad' => $curso['modalidad'] ?? '',
                    'profesor' => $curso['profesor'] ?? '',
                    'duracion' => $curso['duracion'] ?? '',
                    'precio' => $curso['precio'] ?? null,
                    'idioma' => $curso['idioma'] ?? null,
                    'detalleUrl' => $curso['detalleUrl'] ?? '',
                ];
            }, $salida['cursos']);
        }

        if (isset($salida['destacados']) && is_array($salida['destacados'])) {
            $salida['destacados'] = array_map(function (array $curso) {
                return [
                    'id' => $curso['id'] ?? null,
                    'titulo' => $curso['titulo'] ?? '',
                    'descripcion' => $curso['descripcion'] ?? '',
                    'nivel' => $curso['nivel'] ?? '',
                    'modalidad' => $curso['modalidad'] ?? '',
                    'profesor' => $curso['profesor'] ?? '',
                    'duracion' => $curso['duracion'] ?? '',
                    'precio' => $curso['precio'] ?? null,
                    'idioma' => $curso['idioma'] ?? null,
                    'detalleUrl' => $curso['detalleUrl'] ?? '',
                ];
            }, $salida['destacados']);
        }

        if (($salida['modo'] ?? '') === 'sin_resultados') {
            $salida['mensaje'] = $mensajeSinResultados;
        }

        return $this->json($salida);
    }
}