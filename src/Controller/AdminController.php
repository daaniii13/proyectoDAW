<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controlador de entrada al panel de administración
class AdminController extends AbstractController
{
    // Redirige al administrador al listado de usuarios al acceder a /admin
    #[Route('/admin', name: 'admin', methods: ['GET'])]
    public function index(): Response
    {
        // Verifica que el usuario tenga el rol de administrador antes de continuar
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Redirige a la gestión de usuarios, que es la página principal del panel admin
        return $this->redirectToRoute('app_admin_usuarios');
    }
}