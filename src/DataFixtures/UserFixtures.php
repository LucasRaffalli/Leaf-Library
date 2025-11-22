<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roleRepository = $manager->getRepository(Role::class);
        $adminRole = $roleRepository->findOneBy(['name' => 'ROLE_ADMIN']);
        $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
        $librarianRole = $roleRepository->findOneBy(['name' => 'ROLE_LIBRARIAN']);

        $admin = new User();
        $admin->setName('Administrateur');
        $admin->setEmail('admin@leaflibrary.com');
        if ($adminRole) {
            $admin->addUserRole($adminRole);
        }
        $admin->setIsActive(true);
        $admin->setCreatedAt(new \DateTimeImmutable());
        $admin->setUpdateAt(new \DateTimeImmutable());
        
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);
        
        $manager->persist($admin);

        $user = new User();
        $user->setName('Utilisateur Test');
        $user->setEmail('user@leaflibrary.com');
        if ($userRole) {
            $user->addUserRole($userRole);
        }
        $user->setIsActive(true);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdateAt(new \DateTimeImmutable());
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'user123');
        $user->setPassword($hashedPassword);
        
        $manager->persist($user);

        $librarian = new User();
        $librarian->setName('Bibliothécaire');
        $librarian->setEmail('librarian@leaflibrary.com');
        if ($librarianRole) {
            $librarian->addUserRole($librarianRole);
        }
        $librarian->setIsActive(true);
        $librarian->setCreatedAt(new \DateTimeImmutable());
        $librarian->setUpdateAt(new \DateTimeImmutable());
        
        $hashedPassword = $this->passwordHasher->hashPassword($librarian, 'librarian123');
        $librarian->setPassword($hashedPassword);
        
        $manager->persist($librarian);

        $manager->flush();
    }
}