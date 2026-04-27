<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de la página de contacto
// Permite enviar un mensaje al equipo de TeachingExplorer mediante un formulario 
// El mensaje se envía por correo electrónico a la cuenta oficial de TeachingExplorer
class ContactoController extends AbstractController
{
    // Muestra el formulario de contacto
    // Valida que todos los campos estén rellenos y que el email tenga formato correcto
    // antes de intentar el envío. Si el mailer falla, muestra el mensaje de error
    #[Route('/contacto', name: 'app_contacto', methods: ['GET', 'POST'])]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $nombre = trim((string) $request->request->get('nombre'));
            $correo = trim((string) $request->request->get('correo'));
            $mensaje = trim((string) $request->request->get('mensaje'));

            if ($nombre === '' || $correo === '' || $mensaje === '') {
                $this->addFlash('error', 'Debes completar todos los campos.');
                return $this->redirectToRoute('app_contacto');
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'El correo no es válido.');
                return $this->redirectToRoute('app_contacto');
            }

            try {
                $email = (new Email())
                    ->from('teachingexplorerdaw@gmail.com')
                    ->to('teachingexplorerdaw@gmail.com')
                    ->replyTo($correo)
                    ->subject('Nuevo mensaje de contacto - ' . $nombre)
                    ->text(
                        "Nombre: {$nombre}\n" .
                        "Correo de respuesta: {$correo}\n\n" .
                        "Mensaje:\n{$mensaje}"
                    )
                    ->html(
                        '<h2>Nuevo mensaje de contacto</h2>' .
                        '<p><strong>Nombre:</strong> ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</p>' .
                        '<p><strong>Correo de respuesta:</strong> ' . htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') . '</p>' .
                        '<hr>' .
                        '<p><strong>Mensaje:</strong></p>' .
                        '<p>' . nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8')) . '</p>'
                    );

                $mailer->send($email);

                $this->addFlash('success', 'Mensaje enviado correctamente.');
            } catch (\Throwable $e) {
                $this->addFlash('error', 'No se pudo enviar el mensaje: ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_contacto');
        }

        return $this->render('paginas/contacto/index.html.twig');
    }
}
