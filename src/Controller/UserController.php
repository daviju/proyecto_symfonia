<?php

namespace App\Controller;

use App\Entity\Pregunta;
use App\Entity\Respuesta;

use App\Repository\PreguntaRepository;
use App\Repository\RespuestaRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/usuario/preguntas', name: 'usuario_preguntas')]
    #[IsGranted('ROLE_USER')]
    public function mostrarPreguntasActivas(
        PreguntaRepository $preguntaRepository,
        RespuestaRepository $respuestaRepository
    ): Response {
        $user = $this->getUser();

        // Obtener todas las preguntas activas
        $preguntasActivas = $preguntaRepository->findActivePreguntasByUser($user);

        // Obtener las respuestas existentes del usuario
        $respuestasUsuario = $respuestaRepository->findBy(['user' => $user]);
        $respuestasPorPregunta = [];
        foreach ($respuestasUsuario as $respuesta) {
            $respuestasPorPregunta[$respuesta->getPregunta()->getId()] = $respuesta;
        }

        return $this->render('usuario/preguntas/lista.html.twig', [
            'preguntas' => $preguntasActivas,
            'respuestasPorPregunta' => $respuestasPorPregunta,
        ]);
    }

    /**
     * Permite al usuario responder a una pregunta
     */
    #[Route('/preguntas/contestar/{id}', name: 'responder_pregunta')]
    #[IsGranted('ROLE_USER')]
    public function responderPregunta(
        Pregunta $pregunta,
        EntityManagerInterface $entityManager
    ): Response {
        // Verificar si la pregunta está activa
        $fechaActual = new \DateTime();
        if (
            $pregunta->getFechaInicio() > $fechaActual ||
            ($pregunta->getFechaFin() && $pregunta->getFechaFin() < $fechaActual)
        ) {
            $this->addFlash('error', 'Esta pregunta no está activa.');
            return $this->redirectToRoute('usuario_preguntas');
        }

        // Verificar si el usuario ya ha respondido
        $user = $this->getUser();
        $respuestaExistente = $entityManager->getRepository(Respuesta::class)
            ->findOneBy([
                'pregunta' => $pregunta,
                'user' => $user
            ]);

        if ($respuestaExistente) {
            $this->addFlash('error', 'Ya has respondido a esta pregunta.');
            return $this->redirectToRoute('usuario_preguntas');
        }

        // Renderizar la vista de respuesta
        return $this->render('usuario/preguntas/pregunta.html.twig', [
            'pregunta' => $pregunta
        ]);
    }

    #[Route('/preguntas/guardar/{id}', name: 'guardar_respuesta', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function guardarRespuesta(
        Request $request,
        Pregunta $pregunta,
        EntityManagerInterface $entityManager
    ): Response {
        // Validaciones previas (fechas, ya respondida) como en el método anterior

        $respuestaSeleccionada = $request->request->get('respuesta');

        if (!$respuestaSeleccionada) {
            $this->addFlash('error', 'Debe seleccionar una respuesta.');
            return $this->redirectToRoute('responder_pregunta', ['id' => $pregunta->getId()]);
        }

        // Crear y guardar la respuesta
        $respuesta = new Respuesta();
        $respuesta->setPregunta($pregunta);
        $respuesta->setUser($this->getUser());
        $respuesta->setRespuesta((int)$respuestaSeleccionada);
        $respuesta->setFechaRespuesta(new \DateTime());

        $entityManager->persist($respuesta);
        $entityManager->flush();

        $this->addFlash('success', '¡Respuesta guardada correctamente!');
        return $this->redirectToRoute('usuario_preguntas');
    }
}
