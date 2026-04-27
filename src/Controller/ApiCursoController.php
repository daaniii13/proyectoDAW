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

        $historialBusquedas = $session->get('busquedas_recientes_cursos', $session->get('busquedas', []));
        if ($busqueda !== '') {
            array_unshift($historialBusquedas, $busqueda);
            $historialBusquedas = array_values(array_unique(array_filter($historialBusquedas)));
            $historialBusquedas = array_slice($historialBusquedas, 0, 10);

            $session->set('busquedas_recientes_cursos', $historialBusquedas);
            $session->set('busquedas', $historialBusquedas);
        }

        $mapearCursoParaApi = function ($curso) use ($traductorDinamico, $destino) {
            $idiomaOriginalCurso = $curso->getIdioma() ?: 'es';

            $traducirTexto = function (?string $texto) use ($traductorDinamico, $destino, $idiomaOriginalCurso): string {
                if ($texto === null || trim($texto) === '') {
                    return '';
                }

                if ($destino === $idiomaOriginalCurso) {
                    return $texto;
                }

                return $traductorDinamico->traducir($texto, $destino, $idiomaOriginalCurso);
            };

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

        if ($busqueda !== '') {
            $resultados = method_exists($repo, 'buscarPorTextoEIdioma')
                ? $repo->buscarPorTextoEIdioma($busqueda, $idiomaCurso)
                : array_filter(
                    $repo->buscarPorTexto($busqueda),
                    fn($curso) => ($curso->getIdioma() ?: 'es') === $idiomaCurso
                );

            if (!empty($resultados)) {
                return $this->json([
                    'modo' => 'resultados_busqueda',
                    'mensaje' => '',
                    'cursos' => array_map($mapearCursoParaApi, $resultados),
                    'destacados' => [],
                ]);
            }
        }

        $cursos = method_exists($repo, 'buscarCursosActivosPorIdioma')
            ? $repo->buscarCursosActivosPorIdioma($idiomaCurso)
            : array_filter(
                $repo->buscarCursosActivos(),
                fn($curso) => ($curso->getIdioma() ?: 'es') === $idiomaCurso
            );

        $dataApi = array_map($mapearCursoParaApi, $cursos);

        $idsCursosClicados = $session->get('cursos_clicados_recientes', []);
        if (empty($idsCursosClicados)) {
            $idsCursosClicados = $session->get('clics', []);
        }

        $idsCursosClicados = array_map('intval', $idsCursosClicados);

        $payload = [
            'cursos' => $dataApi,
            'ids_cursos_clicados' => $idsCursosClicados,
        ];

        $tmp = tempnam(sys_get_temp_dir(), 'rec_');
        file_put_contents($tmp, json_encode($payload, JSON_UNESCAPED_UNICODE));

        $script = $this->getParameter('kernel.project_dir') . '/python/recomendador.py';

        $comandos = [
            ['py', $script, $tmp],
            ['python', $script, $tmp],
            ['python3', $script, $tmp],
        ];

        $destacados = [];

        foreach ($comandos as $comando) {
            $process = new Process($comando);
            $process->run();

            if ($process->isSuccessful()) {
                $salida = json_decode($process->getOutput(), true);

                if (is_array($salida) && isset($salida['destacados']) && is_array($salida['destacados'])) {
                    $destacados = $salida['destacados'];
                    break;
                }
            }
        }

        @unlink($tmp);

        if (empty($destacados)) {
            $noClicados = array_values(array_filter(
                $dataApi,
                fn(array $curso) => !in_array((int) $curso['id'], $idsCursosClicados, true)
            ));

            $clicados = array_values(array_filter(
                $dataApi,
                fn(array $curso) => in_array((int) $curso['id'], $idsCursosClicados, true)
            ));

            if (!empty($noClicados)) {
                shuffle($noClicados);
            }

            if (!empty($clicados)) {
                shuffle($clicados);
            }

            $destacados = array_slice(array_merge($noClicados, $clicados), 0, 3);
        }

        $mensajeSinResultados = $destino === 'en'
            ? 'No results were found for this search.'
            : 'No hay resultados para esta búsqueda.';

        return $this->json([
            'modo' => $busqueda === '' ? 'recomendados' : 'sin_resultados',
            'cursos' => [],
            'destacados' => $destacados,
            'mensaje' => $busqueda === '' ? '' : $mensajeSinResultados,
        ]);
    }
}