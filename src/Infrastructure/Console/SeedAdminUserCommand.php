<?php

namespace App\Infrastructure\Console;

use App\Infrastructure\Persistence\Doctrine\Entity\UserEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:users:seed-admin', description: 'Seed an admin user')]
final class SeedAdminUserCommand extends Command
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;
    private string $email;
    private string $password;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        string $adminEmail,
        string $adminPassword,
    ) {
        parent::__construct();
        $this->em = $em;
        $this->hasher = $hasher;
        $this->email = $adminEmail;
        $this->password = $adminPassword;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(UserEntity::class);
        $existing = $repo->findOneBy(['email' => $this->email]);
        if ($existing instanceof UserEntity) {
            $output->writeln('Admin already exists');

            return Command::SUCCESS;
        }

        $admin = new UserEntity(
            \Symfony\Component\Uid\Uuid::v4()->toRfc4122(),
            0,
            'Admin',
            'admin',
            $this->email,
            null,
            null,
            null,
            null
        );
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPasswordHash($this->hasher->hashPassword($admin, $this->password));
        $this->em->persist($admin);
        $this->em->flush();

        $output->writeln('Admin created');

        return Command::SUCCESS;
    }
}
