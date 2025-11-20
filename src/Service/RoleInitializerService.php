<?php

namespace App\Service;

use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;

class RoleInitializerService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function ensureBasicRolesExist(): void
    {
        $roleRepository = $this->entityManager->getRepository(Role::class);
        
        $basicRoles = [
            ['name' => 'USER', 'description' => 'Utilisateur standard de la bibliothèque'],
            ['name' => 'LIBRARIAN', 'description' => 'Bibliothécaire - Gestion des emprunts et retours'],
            ['name' => 'ADMIN', 'description' => 'Administrateur - Accès complet au système'],
        ];

        $hasNewRoles = false;
        
        foreach ($basicRoles as $roleData) {
            $existingRole = $roleRepository->findOneBy(['name' => $roleData['name']]);
            
            if (!$existingRole) {
                $role = new Role();
                $role->setName($roleData['name']);
                $role->setDescription($roleData['description']);
                $this->entityManager->persist($role);
                $hasNewRoles = true;
            }
        }
        
        if ($hasNewRoles) {
            $this->entityManager->flush();
        }
    }

    public function getRoleByName(string $name): Role
    {
        $role = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $name]);
        
        if (!$role) {
            $this->ensureBasicRolesExist();
            $role = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => $name]);
        }
        
        if (!$role) {
            throw new \RuntimeException("Le rôle '$name' n'a pas pu être créé");
        }
        
        return $role;
    }
}