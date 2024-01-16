<?php

// src/Command/CreateUserCommand.php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Creates a new user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $username = $helper->ask($input, $output, new Question('Enter username: '));
        $password = $helper->ask($input, $output, new Question('Enter password: '));
        $email = $helper->ask($input, $output, new Question('Enter email: '));

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $password));
        $user->setEmail($email);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created successfully.');

        return Command::SUCCESS;
    }
}
