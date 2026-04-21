<?php

namespace App\Controller;

use App\Entity\Curso;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de gestión de cursos del profesor
// Permite al profesor y al administrador editar los datos de un curso existente y eliminarlo de la plataforma
class ProfesorCursoController extends AbstractController
{
    // Muestra el formulario de edición de un curso y procesa los cambios
    // Valida que todos los campos obligatorios estén rellenos, que el estado sea válido
    // y que el precio sea un número positivo antes de guardar
    #[Route('/profesor/curso/{id}/editar', name: 'app_profesor_editar_curso', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editar(Curso $curso, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_PROFESOR')
        ) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (!$usuario instanceof User || $curso->getProfesor()?->getId() !== $usuario->getId())
        ) {
            throw $this->createAccessDeniedException('No puedes editar un curso que no es tuyo.');
        }

        $errorCurso = null;

        // Inicializa el formulario con los valores actuales del curso
        $datosCurso = [
            'titulo' => $curso->getTitulo(),
            'descripcion' => $curso->getDescripcion(),
            'nivel' => $curso->getNivel(),
            'modalidad' => $curso->getModalidad(),
            'duracion' => $curso->getDuracion(),
            'precio' => $curso->getPrecio(),
            'estado' => $curso->getEstado(),
            'banner_url' => $curso->getBannerUrl(),
            'mapa_url' => $curso->getMapaUrl(),
        ];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('editar_curso_' . $curso->getId(), (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF no válido.');
            }

            $titulo = trim((string) $request->request->get('titulo_curso'));
            $descripcion = trim((string) $request->request->get('descripcion_curso'));
            $nivel = trim((string) $request->request->get('nivel_curso'));
            $modalidad = trim((string) $request->request->get('modalidad_curso'));
            $duracion = trim((string) $request->request->get('duracion_curso'));
            $precio = trim((string) $request->request->get('precio_curso'));
            $estado = trim((string) $request->request->get('estado_curso'));
            $bannerUrl = trim((string) $request->request->get('banner_url'));
            $mapaUrl = trim((string) $request->request->get('mapa_url'));

            $datosCurso = [
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'nivel' => $nivel,
                'modalidad' => $modalidad,
                'duracion' => $duracion,
                'precio' => $precio,
                'estado' => $estado,
                'banner_url' => $bannerUrl,
                'mapa_url' => $mapaUrl,
            ];

            if (
                $titulo === '' || $descripcion === '' || $nivel === '' ||
                $modalidad === '' || $duracion === '' || $precio === '' || $estado === ''
            ) {
                $errorCurso = 'Todos los campos obligatorios del curso son necesarios.';
            } elseif (!in_array($estado, ['activo', 'borrador', 'cerrado'], true)) {
                $errorCurso = 'El estado del curso no es válido.';
            } elseif (!is_numeric($precio) || (float) $precio < 0) {
                $errorCurso = 'El precio debe ser un número válido igual o mayor que 0.';
            } else {
                $curso->setTitulo($titulo);
                $curso->setDescripcion($descripcion);
                $curso->setNivel($nivel);
                $curso->setModalidad($modalidad);
                $curso->setDuracion($duracion);
                $curso->setPrecio(number_format((float) $precio, 2, '.', ''));
                $curso->setEstado($estado);
                $curso->setBannerUrl($bannerUrl !== '' ? $bannerUrl : null);
                $curso->setMapaUrl($mapaUrl !== '' ? $mapaUrl : null);

                $entityManager->flush();

                $this->addFlash('success', 'Curso actualizado correctamente.');

                return $this->redirectToRoute('app_subir_recursos');
            }
        }

        return $this->render('paginas/profesor/editar_curso.html.twig', [
            'curso' => $curso,
            'errorCurso' => $errorCurso,
            'datosCurso' => $datosCurso,
        ]);
    }

    // Elimina un curso de la plataforma
    // El profesor solo puede eliminar sus propios cursos. El administrador puede eliminar cualquier curso
    #[Route('/profesor/curso/{id}/eliminar', name: 'app_profesor_eliminar_curso', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function eliminar(Curso $curso, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (
            !$this->isGranted('ROLE_ADMIN') &&
            !$this->isGranted('ROLE_PROFESOR')
        ) {
            throw $this->createAccessDeniedException('No tienes acceso a esta sección.');
        }

        /** @var User|null $usuario */
        $usuario = $this->getUser();

        if (
            !$this->isGranted('ROLE_ADMIN') &&
            (!$usuario instanceof User || $curso->getProfesor()?->getId() !== $usuario->getId())
        ) {
            throw $this->createAccessDeniedException('No puedes eliminar un curso que no es tuyo.');
        }

        if (!$this->isCsrfTokenValid('eliminar_curso_' . $curso->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $entityManager->remove($curso);
        $entityManager->flush();

        $this->addFlash('success', 'Curso eliminado correctamente.');

        return $this->redirectToRoute('app_subir_recursos');
    }
}
