<?php

namespace App\DataFixtures;

use App\Entity\BookStatus;
use App\Entity\BookCondition;
use App\Entity\Category;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $statusAvailable = new BookStatus();
        $statusAvailable->setName('Disponible');
        $manager->persist($statusAvailable);

        $statusBorrowed = new BookStatus();
        $statusBorrowed->setName('Emprunté');
        $manager->persist($statusBorrowed);

        $conditionNew = new BookCondition();
        $conditionNew->setName('Neuf');
        $manager->persist($conditionNew);

        $conditionGood = new BookCondition();
        $conditionGood->setName('Bon état');
        $manager->persist($conditionGood);

        $conditionUsed = new BookCondition();
        $conditionUsed->setName('Usagé');
        $manager->persist($conditionUsed);

        $categories = [];
        $categoryNames = ['Roman', 'Science-Fiction', 'Histoire', 'Biographie', 'Technique', 'Jeunesse'];
        
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        $manager->flush();

        $booksData = [
            [
                'title' => 'Les Misérables',
                'author' => 'Victor Hugo',
                'depositAmount' => '15.00',
                'status' => $statusAvailable,
                'condition' => $conditionGood,
                'categories' => [$categories[0]] // Roman
            ],
            [
                'title' => 'Dune',
                'author' => 'Frank Herbert',
                'depositAmount' => '20.00',
                'status' => $statusBorrowed,
                'condition' => $conditionNew,
                'categories' => [$categories[1]] // Science-Fiction
            ],
            [
                'title' => 'Steve Jobs',
                'author' => 'Walter Isaacson',
                'depositAmount' => '18.00',
                'status' => $statusAvailable,
                'condition' => $conditionNew,
                'categories' => [$categories[3]] // Biographie
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'depositAmount' => '25.00',
                'status' => $statusAvailable,
                'condition' => $conditionGood,
                'categories' => [$categories[4]] // Technique
            ],
            [
                'title' => 'Le Petit Prince',
                'author' => 'Antoine de Saint-Exupéry',
                'depositAmount' => '10.00',
                'status' => $statusAvailable,
                'condition' => $conditionUsed,
                'categories' => [$categories[0], $categories[5]] // Roman + Jeunesse
            ]
        ];

        foreach ($booksData as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setAuthor($bookData['author']);
            $book->setDepositAmount($bookData['depositAmount']);
            $book->setStatus($bookData['status']);
            $book->setCondition($bookData['condition']);
            $book->setIsArchived(false);
            $book->setCreatedAt(new \DateTimeImmutable());
            $book->setUpdatedAt(new \DateTimeImmutable());
            
            foreach ($bookData['categories'] as $category) {
                $book->addCategory($category);
            }
            
            $manager->persist($book);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixtures::class,
        ];
    }
}