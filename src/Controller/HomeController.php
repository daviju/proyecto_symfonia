<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Si estas registrado y eres admin te manda a  la pagina de admin

        // Si estas registrado y eres usuario te manda a la pagina de usuario

        // Si no estas registrado te manda a la pagina de login

        $user = $this->getUser();

        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('admin_preguntas');
        } elseif ($user && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('usuario_preguntas');
        }

        return $this->redirectToRoute('app_login');
    }
}
