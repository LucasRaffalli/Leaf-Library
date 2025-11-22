<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Borrow;
use App\Entity\User;
use App\Exception\BookNotAvailableException;
use App\Exception\BorrowLimitExceededException;
use App\Exception\UserNotActiveException;
use App\Repository\BorrowRepository;

class BorrowValidator
{
    public function __construct(
        private BorrowRepository $borrowRepository
    ) {
    }


    public function validateBookCanBeBorrowed(Book $book): void
    {
        if ($book->isArchived()) {
            throw new BookNotAvailableException('Ce livre est archivé et ne peut pas être emprunté.');
        }

        if ($this->borrowRepository->hasActiveBorrow($book)) {
            $activeBorrow = $this->borrowRepository->findActiveBorrowForBook($book);
            $returnDate = $activeBorrow?->getReturnDueDate()?->format('d/m/Y');
            throw new BookNotAvailableException(
                "Ce livre est déjà emprunté jusqu'au $returnDate. Veuillez attendre qu'il soit retourné."
            );
        }
    }

    public function validateUserCanBorrow(User $user, int $maxBorrowsPerUser = 5): void
    {
        if (!$user->isActive()) {
            throw new UserNotActiveException('Votre compte est désactivé. Vous ne pouvez pas emprunter de livres.');
        }

        $activeBorrows = $this->borrowRepository->findActiveBorrowsForUser($user);
        if (count($activeBorrows) >= $maxBorrowsPerUser) {
            throw new BorrowLimitExceededException(
                $maxBorrowsPerUser,
                "Vous avez atteint le nombre maximum d'emprunts simultanés ($maxBorrowsPerUser). Veuillez retourner un livre avant d'en emprunter un nouveau."
            );
        }
    }

    public function validateBorrow(Book $book, User $user): void
    {
        $this->validateBookCanBeBorrowed($book);
        $this->validateUserCanBorrow($user);
    }
}
