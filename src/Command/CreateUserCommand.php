<?php

namespace App\Command;

// Importamos las clases necesarias
use App\Entity\User; // Entidad User para interactuar con la base de datos
use Doctrine\ORM\EntityManagerInterface; // EntityManager para operaciones en la base de datos
use Symfony\Component\Console\Attribute\AsCommand; // Permite definir comandos mediante atributos
use Symfony\Component\Console\Command\Command; // Clase base para crear comandos
use Symfony\Component\Console\Input\InputInterface; // Permite manejar entradas del usuario
use Symfony\Component\Console\Output\OutputInterface; // Permite manejar salidas hacia la consola
use Symfony\Component\Console\Style\SymfonyStyle; // Proporciona un estilo mejorado para la interacción en consola
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Componente para hashear contraseñas

#[AsCommand(
    name: 'app:create-user', // Nombre del comando que se ejecuta en consola
    description: 'Crea un nuevo usuario en la base de datos.', // Descripción del comando
)]
class CreateUserCommand extends Command
{
    // Propiedades para el EntityManager y el PasswordHasher
    private EntityManagerInterface $entityManager; // Manejador de entidades para interactuar con la base de datos
    private UserPasswordHasherInterface $passwordHasher; // Para cifrar contraseñas

    // Constructor para inicializar las dependencias necesarias
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct(); // Llamada al constructor de la clase base
        $this->entityManager = $entityManager; // Inyectamos el EntityManager
        $this->passwordHasher = $passwordHasher; // Inyectamos el PasswordHasher
    }

    // Método principal que se ejecuta al llamar al comando
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Estilo mejorado para las interacciones con el usuario en la consola
        $io = new SymfonyStyle($input, $output);

        // Solicitar los campos necesarios al usuario
        $email = $io->ask('Introduce el correo electrónico del usuario'); // Solicita el email
        $password = $io->askHidden('Introduce la contraseña del usuario (oculta mientras escribes)'); // Solicita la contraseña de forma oculta
        $rolesInput = $io->ask('Introduce los roles del usuario separados por comas (por defecto: ROLE_USER)', 'ROLE_USER'); // Roles por defecto si no se especifican

        // Convertimos la lista de roles en un array
        $roles = explode(',', $rolesInput);

        // Verificamos si ya existe un usuario con el mismo email
        $userRepository = $this->entityManager->getRepository(User::class); // Obtenemos el repositorio de la entidad User
        if ($userRepository->findOneBy(['email' => $email])) { // Buscamos si ya existe un usuario con ese email
            $io->error('El usuario ya existe.'); // Mostramos un error si existe
            return Command::FAILURE; // Finalizamos con un estado de fallo
        }

        // Crear y configurar el nuevo usuario
        $user = new User(); // Instanciamos un nuevo usuario
        $user->setEmail($email); // Asignamos el email
        $user->setRoles($roles); // Asignamos los roles
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password); // Ciframos la contraseña
        $user->setPassword($hashedPassword); // Asignamos la contraseña cifrada

        // Persistimos y guardamos el usuario en la base de datos
        $this->entityManager->persist($user); // Marcamos el usuario como listo para guardar
        $this->entityManager->flush(); // Guardamos el usuario en la base de datos

        // Mostramos un mensaje de éxito al usuario
        $io->success('Usuario creado con éxito:');
        $io->listing([ // Mostramos los detalles del usuario creado
            'Email: ' . $email,
            'Roles: ' . implode(', ', $roles), // Convertimos los roles de vuelta en una cadena para mostrar
        ]);

        return Command::SUCCESS; // Finalizamos con un estado de éxito
    }
}
