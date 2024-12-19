<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var string $plainPassword */
                $plainPassword = $form->get('plainPassword')->getData();

                // Codificar la contraseña
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

                // Persistir el usuario en la base de datos
                $entityManager->persist($user);
                $entityManager->flush();

                // Enviar el email de bienvenida
                try {
                    $this->emailService->sendRegistrationEmail($user->getEmail());
                    $this->addFlash('success', '¡Registro completado! Se ha enviado un email de bienvenida.');
                } catch (\Exception $e) {
                    // Si falla el envío del email, logueamos el error pero permitimos continuar
                    $this->addFlash('warning', 'El registro se completó pero hubo un problema al enviar el email de bienvenida.');
                    // Aquí podrías añadir un log del error
                }

                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error durante el registro. Por favor, inténtalo de nuevo.');
                // Aquí podrías añadir un log del error
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}