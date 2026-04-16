<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

// ErrorController gestiona las páginas de error personalizadas de la aplicación.
class ErrorController extends AbstractController
{
    // Ruta accesible manualmente para previsualizar la página de error 404
    #[Route('/pagina-no-encontrada', name: 'app_404_demo')]
    public function notFound()
    {
        return $this->render('paginas/errores/404.html.twig');
    }
}