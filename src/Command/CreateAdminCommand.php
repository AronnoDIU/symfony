<?php

// src/Command/CreateAdminCommand.php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommand extends Command
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
            ->setName('app:create-admin')
            ->setDescription('Creates an admin user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $output->writeln('Admin user created successfully.');

        return Command::SUCCESS;
    }
}
