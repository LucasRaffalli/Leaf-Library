<?php

namespace App\DataFixtures;

use App\Entity\BorrowStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BorrowStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            [
                'name' => 'En cours',
                'reference' => 'status_en_cours'
            ],
            [
                'name' => 'Rendu',
                'reference' => 'status_rendu'
            ],
            [
                'name' => 'En retard',
                'reference' => 'status_en_retard'
            ]
        ];

        foreach ($statuses as $statusData) {
            $status = new BorrowStatus();
            $status->setName($statusData['name']);
            
            $manager->persist($status);
            $this->addReference($statusData['reference'], $status);
        }

        $manager->flush();
    }
}
