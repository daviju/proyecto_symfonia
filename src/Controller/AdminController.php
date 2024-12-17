<?php

namespace App\Controller;

use App\Entity\Pregunta;
use App\Entity\Respuesta;
use App\Form\PreguntaType;
use App\Repository\PreguntaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/preguntas', name: 'admin_preguntas')]
    public function index(PreguntaRepository $preguntaRepository): Response
    {
        $preguntas = $preguntaRepository->findAll();
        return $this->render('admin/preguntas/index.html.twig', [
            'preguntas' => $preguntas,
        ]);
    }

    #[Route('/admin/preguntas/new', name: 'admin_preguntas_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pregunta = new Pregunta();
        $pregunta->setFechaInicio(new \DateTime()); // Establecer fecha actual

        $form = $this->createForm(PreguntaType::class, $pregunta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pregunta);
            $entityManager->flush();

            // Crear las respuestas de la pregunta
            $respuestasTexto = [
                $pregunta->getR1(),
                $pregunta->getR2(),
                $pregunta->getR3(),
                $pregunta->getR4(),
            ];

            foreach ($respuestasTexto as $indice => $respuestaTexto) {
                if ($respuestaTexto) {
                    $respuesta = new Respuesta();
                    $respuesta->setRespuesta($indice + 1);
                    $respuesta->setPregunta($pregunta);
                    $respuesta->setFechaRespuesta(new \DateTime()); // Establecer fecha actual
                    $entityManager->persist($respuesta);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Pregunta creada correctamente.');
            return $this->redirectToRoute('admin_preguntas');
        }

        return $this->render('admin/preguntas/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/preguntas/edit/{id}', name: 'admin_preguntas_edit')]
    public function edit(Request $request, Pregunta $pregunta, EntityManagerInterface $em): Response
    {
        // Crear el formulario de edición
        $form = $this->createForm(PreguntaType::class, $pregunta);
    
        // Manejar el envío del formulario
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Guardar los cambios en la base de datos
            $em->flush();
    
            // Redirigir a la vista de detalles de la pregunta
            return $this->redirectToRoute('admin_preguntas_show', ['id' => $pregunta->getId()]);
        }
    
        return $this->render('admin/preguntas/edit.html.twig', [
            'form' => $form->createView(),
            'pregunta' => $pregunta,
        ]);
    }
    

    #[Route('/admin/preguntas/delete/{id}', name: 'admin_preguntas_delete')]
    public function delete(Pregunta $pregunta, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($pregunta);
        $entityManager->flush();

        $this->addFlash('success', 'Pregunta eliminada correctamente.');

        return $this->redirectToRoute('admin_preguntas');
    }

    #[Route('/admin/preguntas/show/{id}', name: 'admin_preguntas_show')]
    public function show(Pregunta $pregunta): Response
    {
        return $this->render('admin/preguntas/show.html.twig', [
            'pregunta' => $pregunta,
        ]);
    }
}
