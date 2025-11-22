<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BorrowRepository;


class BookAvailabilityService
{
    public function __construct(
        private BorrowRepository $borrowRepository
    ) {
    }

    public function hasActiveBorrow(Book $book): bool
    {
        return $this->borrowRepository->hasActiveBorrow($book);
    }


    public function getActiveBorrow(Book $book): ?\App\Entity\Borrow
    {
        return $this->borrowRepository->findActiveBorrowForBook($book);
    }

    public function isAvailableForBorrow(Book $book): bool
    {
        return !$book->isArchived() && !$this->hasActiveBorrow($book);
    }
}
