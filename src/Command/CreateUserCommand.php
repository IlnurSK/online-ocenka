<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a test user',
)]
class CreateUserCommand extends Command
{
    private $entityManager;
    private $hasher;
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created!');
        return Command::SUCCESS;
    }
}
