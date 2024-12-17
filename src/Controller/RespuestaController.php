<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RespuestaController extends AbstractController
{
    #[Route('/respuesta', name: 'app_respuesta')]
    public function index(): Response
    {
        return $this->render('respuesta/index.html.twig', [
            'controller_name' => 'RespuestaController',
        ]);
    }
}
