<?php

namespace App\Command;

use App\Entity\Role;
use App\Entity\User;
use App\Service\RoleInitializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:add-role',
    description: 'Ajouter un rôle à un utilisateur',
)]
class AddRoleToUserCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager, private RoleInitializerService $roleInitializer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('role', InputArgument::REQUIRED, 'Nom du rôle (USER, LIBRARIAN, ADMIN)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $roleName = strtoupper($input->getArgument('role'));

        $userRepository = $this->entityManager->getRepository(User::class);

        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            $io->error("Utilisateur avec l'email '$email' introuvable.");
            return Command::FAILURE;
        }

        try {
            $role = $this->roleInitializer->getRoleByName($roleName);
            $user->addUserRole($role);
            $this->entityManager->flush();
            
            $io->success("Rôle '$roleName' ajouté à l'utilisateur '$email'.");
            return Command::SUCCESS;
        } catch (\RuntimeException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}