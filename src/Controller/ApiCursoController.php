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
        $locale = (string) $request->getLocale();
        $destino = str_starts_with(strtolower($locale), 'en') ? 'en' : 'es';

        if ($busqueda !== '') {
            $historialBusquedas = $session->get('busquedas', []);
            array_unshift($historialBusquedas, $busqueda);
            $session->set('busquedas', array_slice(array_unique($historialBusquedas), 0, 10));
        }

        $traducirTexto = function (?string $texto) use ($traductorDinamico, $destino): string {
            if ($texto === null || trim($texto) === '') {
                return '';
            }

            return $destino === 'en'
                ? $traductorDinamico->traducir($texto, 'en', 'es')
                : $texto;
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
                'detalleUrl' => $this->generateUrl('app_detalle_curso', ['id' => $curso->getId()]),
            ];
        };

        if ($busqueda !== '') {
            $resultados = $repo->buscarPorTexto($busqueda);

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

        $cursos = $repo->buscarCursosActivos();

        $data = array_map($mapearCurso, $cursos);

        $idsCursosClicados = $session->get('cursos_clicados_recientes', []);
        if (empty($idsCursosClicados)) {
            $idsCursosClicados = $session->get('clics', []);
        }

        $payload = [
            'cursos' => $data,
            'busqueda_actual' => $busqueda,
            'busquedas_recientes' => $session->get('busquedas', []),
            'ids_cursos_clicados' => $idsCursosClicados,
        ];

        $tmp = tempnam(sys_get_temp_dir(), 'rec_');
        file_put_contents($tmp, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $script = $this->getParameter('kernel.project_dir') . '/python/recomendador.py';

        $process = new Process(['python', $script, $tmp]);
        $process->run();

        unlink($tmp);

        $mensajeSinResultados = $destino === 'en'
            ? 'No results were found for this search.'
            : 'No hay resultados para esta búsqueda.';

        if (!$process->isSuccessful()) {
            return $this->json([
                'modo' => $busqueda === '' ? 'recomendados' : 'sin_resultados',
                'cursos' => [],
                'destacados' => array_slice($data, 0, 3),
                'mensaje' => $busqueda === '' ? '' : $mensajeSinResultados,
            ]);
        }

        $salida = json_decode($process->getOutput(), true);

        if (!is_array($salida)) {
            return $this->json([
                'modo' => $busqueda === '' ? 'recomendados' : 'sin_resultados',
                'cursos' => [],
                'destacados' => array_slice($data, 0, 3),
                'mensaje' => $busqueda === '' ? '' : $mensajeSinResultados,
            ]);
        }

        if (isset($salida['cursos']) && is_array($salida['cursos'])) {
            $salida['cursos'] = array_map(function (array $curso) use ($traducirTexto) {
                return [
                    'id' => $curso['id'] ?? null,
                    'titulo' => $traducirTexto($curso['titulo'] ?? ''),
                    'descripcion' => $traducirTexto($curso['descripcion'] ?? ''),
                    'nivel' => $traducirTexto($curso['nivel'] ?? ''),
                    'modalidad' => $traducirTexto($curso['modalidad'] ?? ''),
                    'profesor' => $curso['profesor'] ?? '',
                    'duracion' => $traducirTexto($curso['duracion'] ?? ''),
                    'precio' => $curso['precio'] ?? null,
                    'detalleUrl' => $curso['detalleUrl'] ?? '',
                ];
            }, $salida['cursos']);
        }

        if (isset($salida['destacados']) && is_array($salida['destacados'])) {
            $salida['destacados'] = array_map(function (array $curso) use ($traducirTexto) {
                return [
                    'id' => $curso['id'] ?? null,
                    'titulo' => $traducirTexto($curso['titulo'] ?? ''),
                    'descripcion' => $traducirTexto($curso['descripcion'] ?? ''),
                    'nivel' => $traducirTexto($curso['nivel'] ?? ''),
                    'modalidad' => $traducirTexto($curso['modalidad'] ?? ''),
                    'profesor' => $curso['profesor'] ?? '',
                    'duracion' => $traducirTexto($curso['duracion'] ?? ''),
                    'precio' => $curso['precio'] ?? null,
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