<?php

namespace App\Controller;

use App\Entity\EntregaComentario;
use App\Entity\EntregaTarea;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador para el sistema de mensajería interno entre profesor y alumno sobre una entrega
class EntregaComentarioController extends AbstractController
{
    // Añade un mensaje al hilo de comentarios de una entrega
    // El autor del mensaje es el usuario autenticado en ese momento
    #[Route('/entrega/{id}/responder', name: 'app_responder_entrega', methods: ['POST'])]
    public function responder(
        EntregaTarea $entrega,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $texto = trim((string) $request->request->get('mensaje'));

        // Si el mensaje está vacío, redirige sin crear ningún registro
        if ($texto === '') {
            return $this->redirectToRoute('app_profesor_tareas_curso', [
                'id' => $entrega->getTarea()->getCurso()->getId(),
            ]);
        }

        $msg = new EntregaComentario();
        $msg->setEntrega($entrega);
        $msg->setAutor($usuario);
        $msg->setMensaje($texto);

        $em->persist($msg);
        $em->flush();

        return $this->redirectToRoute('app_profesor_tareas_curso', [
            'id' => $entrega->getTarea()->getCurso()->getId(),
        ]);
    }
}