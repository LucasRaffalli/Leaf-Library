<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = [
            [
                'name' => 'ROLE_USER',
                'description' => 'Utilisateur standard de la bibliothèque'
            ],
            [
                'name' => 'ROLE_LIBRARIAN',
                'description' => 'Bibliothécaire - Gestion des emprunts et retours'
            ],
            [
                'name' => 'ROLE_ADMIN',
                'description' => 'Administrateur - Accès complet au système'
            ]
        ];

        foreach ($roles as $roleData) {
            $role = new Role();
            $role->setName($roleData['name']);
            $role->setDescription($roleData['description']);
            
            $manager->persist($role);

            $refName = strtolower(str_replace('ROLE_', '', $roleData['name']));
            $this->addReference('role_' . $refName, $role);
        }

        $manager->flush();
    }
}