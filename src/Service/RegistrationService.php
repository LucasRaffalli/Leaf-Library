<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager,
        private RoleInitializerService $roleInitializer
    ) {
    }

    public function registerUser(User $user, string $plainPassword): void
    {
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );

        $userRole = $this->roleInitializer->getRoleByName('USER');
        $user->addUserRole($userRole);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
