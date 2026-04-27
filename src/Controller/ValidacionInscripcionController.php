<?php

namespace App\Controller;

use App\Entity\Inscripcion;
use App\Entity\User;
use App\Repository\InscripcionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

// Controlador de validación del flujo de inscripción
// Gestiona los cambios de estado de una inscripción a lo largo del proceso de pago:
// El alumno avisa que ha pagado y el profesor valida o rechaza la inscripción
class ValidacionInscripcionController extends AbstractController
{
    // El alumno notifica al profesor que ha realizado el pago
    // Cambia el estado de 'pendiente_pago' a 'pendiente_validacion'
    #[Route('/inscripcion/{id}/avisar-pago', name: 'app_avisar_pago_inscripcion', methods: ['POST'])]
    public function avisarPago(
        Inscripcion $inscripcion,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        // Solo el alumno propietario de la inscripción o el admin pueden ejecutar esta acción
        if ($inscripcion->getEstudiante()?->getId() !== $usuario->getId() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No puedes modificar esta inscripción.');
        }

        if (!$this->isCsrfTokenValid('avisar_pago_' . $inscripcion->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        // Solo actualiza si la inscripción está en el estado previo esperado
        if ($inscripcion->getEstado() === 'pendiente_pago') {
            $inscripcion->setEstado('pendiente_validacion');
            $entityManager->flush();
            $this->addFlash('success', 'Pago avisado correctamente. Ahora queda pendiente de validación.');
        }

        return $this->redirectToRoute('app_mis_cursos');
    }

    // El profesor o administrador activa una inscripción tras verificar el pago
    // Cambia el estado a 'activa', dando acceso al alumno al contenido del curso
    #[Route('/profesor/inscripcion/{id}/activar', name: 'app_profesor_activar_inscripcion', methods: ['POST'])]
    public function activar(
        int $id,
        Request $request,
        InscripcionRepository $inscripcionRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_PROFESOR') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No tienes acceso.');
        }

        if (!$this->isCsrfTokenValid('activar_inscripcion_' . $id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        // El admin puede activar cualquier inscripción. El profesor solo las de sus cursos
        $inscripcion = $this->isGranted('ROLE_ADMIN')
            ? $inscripcionRepository->find($id)
            : $inscripcionRepository->buscarInscripcionDeProfesorPorId($usuario, $id);

        if (!$inscripcion) {
            throw $this->createNotFoundException('Inscripción no encontrada.');
        }

        $inscripcion->setEstado('activa');
        $entityManager->flush();

        // Al aceptar la solicitud se envía un correo al alumno para avisarle
        try {
            $alumno = $inscripcion->getEstudiante();
            $curso = $inscripcion->getCurso();
            $nombreAlumno = (string) $alumno->getNombre();
            $tituloCurso = (string) $curso->getTitulo();

            $email = (new Email())
                ->from('teachingexplorerdaw@gmail.com')
                ->to((string) $alumno->getEmail())
                ->subject('Inscripción aceptada - ' . $tituloCurso)
                ->text(
                    "Hola {$nombreAlumno},\n\n" .
                    "Tu solicitud de inscripción en el curso \"{$tituloCurso}\" ha sido aceptada.\n\n" .
                    "Ya puedes acceder al contenido del curso desde tu panel de 'Mis cursos'.\n\n" .
                    "¡Bienvenido!"
                )
                ->html(
                    '<h2>¡Inscripción aceptada!</h2>' .
                    '<p>Hola ' . htmlspecialchars($nombreAlumno, ENT_QUOTES, 'UTF-8') . ',</p>' .
                    '<p>Tu solicitud de inscripción en el curso <strong>' . htmlspecialchars($tituloCurso, ENT_QUOTES, 'UTF-8') . '</strong> ha sido aceptada.</p>' .
                    '<p>Ya puedes acceder al contenido desde tu panel de <em>Mis cursos</em>.</p>' .
                    '<p>¡Bienvenido!</p>'
                );

            $mailer->send($email);
        } catch (\Throwable $e) {
            // Si el correo falla no interrumpimos el flujo. La inscripción ya fue activada correctamente
        }

        $this->addFlash('success', 'Inscripción activada correctamente.');

        return $this->redirectToRoute('app_subir_recursos');
    }

    // El profesor o administrador rechaza una solicitud de inscripción
    // Cambia el estado a 'rechazada', impidiendo el acceso del alumno al curso
    #[Route('/profesor/inscripcion/{id}/rechazar', name: 'app_profesor_rechazar_inscripcion', methods: ['POST'])]
    public function rechazar(
        int $id,
        Request $request,
        InscripcionRepository $inscripcionRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $usuario = $this->getUser();

        if (!$usuario instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isGranted('ROLE_PROFESOR') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No tienes acceso.');
        }

        if (!$this->isCsrfTokenValid('rechazar_inscripcion_' . $id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF no válido.');
        }

        $inscripcion = $this->isGranted('ROLE_ADMIN')
            ? $inscripcionRepository->find($id)
            : $inscripcionRepository->buscarInscripcionDeProfesorPorId($usuario, $id);

        if (!$inscripcion) {
            throw $this->createNotFoundException('Inscripción no encontrada.');
        }

        $inscripcion->setEstado('rechazada');
        $entityManager->flush();

        // Al rechazar la solicitud se envía un correo al alumno para avisarle
        try {
            $alumno = $inscripcion->getEstudiante();
            $curso = $inscripcion->getCurso();
            $nombreAlumno = (string) $alumno->getNombre();
            $tituloCurso = (string) $curso->getTitulo();

            $email = (new Email())
                ->from('teachingexplorerdaw@gmail.com')
                ->to((string) $alumno->getEmail())
                ->subject('Solicitud de inscripción rechazada - ' . $tituloCurso)
                ->text(
                    "Hola {$nombreAlumno},\n\n" .
                    "Lamentablemente, tu solicitud de inscripción en el curso \"{$tituloCurso}\" ha sido rechazada." 
                )
                ->html(
                    '<h2>Solicitud de inscripción rechazada</h2>' .
                    '<p>Hola ' . htmlspecialchars($nombreAlumno, ENT_QUOTES, 'UTF-8') . ',</p>' .
                    '<p>Lamentablemente, tu solicitud de inscripción en el curso <strong>' . htmlspecialchars($tituloCurso, ENT_QUOTES, 'UTF-8') . '</strong> ha sido rechazada.</p>' 
                );

            $mailer->send($email);
        } catch (\Throwable $e) {
            // Si el correo falla no interrumpimos el flujo. El rechazo ya fue registrado correctamente
        }

        $this->addFlash('success', 'Solicitud rechazada correctamente.');

        return $this->redirectToRoute('app_subir_recursos');
    }
}