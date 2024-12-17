<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PreguntaController extends AbstractController
{
    #[Route('/pregunta', name: 'app_pregunta')]
    public function index(): Response
    {
        return $this->render('pregunta/index.html.twig', [
            'controller_name' => 'PreguntaController',
        ]);
    }
}