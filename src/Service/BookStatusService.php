<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookStatusRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookStatusService
{
    public function __construct(
        private BookStatusRepository $bookStatusRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function updateStatusWhenBorrowed(Book $book): void
    {
        $borrowedStatus = $this->bookStatusRepository->findOneBy(['name' => 'Emprunté']);
        
        if ($borrowedStatus) {
            $book->setStatus($borrowedStatus);
            $book->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();
        }
    }

    public function updateStatusWhenReturned(Book $book): void
    {
        $availableStatus = $this->bookStatusRepository->findOneBy(['name' => 'Disponible']);
        
        if ($availableStatus) {
            $book->setStatus($availableStatus);
            $book->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();
        }
    }
}
