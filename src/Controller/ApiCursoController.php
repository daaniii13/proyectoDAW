<?php

namespace App\Controller;

use App\Repository\CursoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

// Registra el historial de búsquedas del usuario en sesión y manda la lógica
// de recomendación a un script Python externo (recomendador.py)
class ApiCursoController extends AbstractController
{
    // Devuelve los cursos activos en formato JSON con recomendaciones personalizadas
    #[Route('/api/cursos', name: 'api_cursos')]
    public function listar(
        Request $request,
        CursoRepository $repo
    ): JsonResponse {
        $busqueda = trim((string) $request->query->get('q', ''));
        $session = $request->getSession();

        // Guarda el término de búsqueda en el historial de la sesión (máximo 10 entradas)
        if ($busqueda !== '') {
            $historialBusquedas = $session->get('busquedas', []);
            array_unshift($historialBusquedas, $busqueda);
            $session->set('busquedas', array_slice(array_unique($historialBusquedas), 0, 10));
        }

        $cursos = $repo->buscarCursosActivos();

        // Mapea las entidades a arrays planos para el recomendador y la vista
        $data = array_map(function ($curso) {
            return [
                'id' => $curso->getId(),
                'titulo' => $curso->getTitulo(),
                'descripcion' => $curso->getDescripcion(),
                'nivel' => $curso->getNivel(),
                'modalidad' => $curso->getModalidad(),
                'profesor' => $curso->getProfesor()?->getNombre(),
                'detalleUrl' => $this->generateUrl('app_detalle_curso', ['id' => $curso->getId()]),
            ];
        }, $cursos);

        // Recupera el historial de cursos visitados. Se comprueba también 'clics'
        // en caso de que ambas claves coexistan en sesiones antiguas
        $idsCursosClicados = $session->get('cursos_clicados_recientes', []);
        if (empty($idsCursosClicados)) {
            $idsCursosClicados = $session->get('clics', []);
        }

        // Construye el contexto que se envía al script Python del recomendador
        $payload = [
            'cursos' => $data,
            'busqueda_actual' => $busqueda,
            'busquedas_recientes' => $session->get('busquedas', []),
            'ids_cursos_clicados' => $idsCursosClicados,
        ];

        // Escribe el contexto en un archivo temporal para pasárselo al proceso Python
        $tmp = tempnam(sys_get_temp_dir(), 'rec_');
        file_put_contents($tmp, json_encode($payload));

        $script = $this->getParameter('kernel.project_dir') . '/python/recomendador.py';
        $process = new Process(['python', $script, $tmp]);
        $process->run();

        // Elimina el archivo temporal independientemente del resultado
        unlink($tmp);

        // Si el proceso falla, devuelve los tres primeros cursos como resultado por defecto
        if (!$process->isSuccessful()) {
            return $this->json([
                'modo' => 'fallback',
                'cursos' => [],
                'destacados' => array_slice($data, 0, 3),
                'mensaje' => '',
            ]);
        }

        // Devuelve directamente la respuesta JSON del recomendador
        return $this->json(json_decode($process->getOutput(), true));
    }
}