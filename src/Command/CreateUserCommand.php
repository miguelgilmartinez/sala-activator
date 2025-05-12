<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @author Miguel Gil Martínez <miguel.gil.martinez@juntadeandalucia.es>
 *
 * Comando de creación de usuarios. 
 *
 * bin/console app:create-admin <email> <password> <nombre> <apellidos>
 */
#[AsCommand(
            name: 'app:create-user',
            description: 'Crea un nuevo usuario',
    )]
class CreateUserCommand extends Command {

    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void {
        $this
                ->addArgument('email', InputArgument::REQUIRED, 'Email del usuario')
                ->addArgument('password', InputArgument::REQUIRED, 'Contraseña del usuario')
                ->addArgument('nombre', InputArgument::REQUIRED, 'Nombre del usuario')
                ->addArgument('apellidos', InputArgument::REQUIRED, 'Apellidos del usuario')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $nombre = $input->getArgument('nombre');
        $apellidos = $input->getArgument('apellidos');

        // Comprobar si el usuario ya existe
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser) {
            $io->error(sprintf('Ya existe un usuario con el email "%s"', $email));
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setNombre($nombre);
        $user->setApellidos($apellidos);

        // Encriptar la contraseña
        $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $password
        );
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Usuario creado: %s', $email));

        return Command::SUCCESS;
    }
}
