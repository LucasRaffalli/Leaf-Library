<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\User;
use App\Exception\BookNotAvailableException;
use App\Exception\BorrowLimitExceededException;
use App\Exception\UserNotActiveException;
use App\Repository\BorrowRepository;
use App\Service\BorrowValidator;
use PHPUnit\Framework\TestCase;

class BorrowValidatorTest extends TestCase
{
    private BorrowRepository $borrowRepository;
    private BorrowValidator $borrowValidator;

    protected function setUp(): void
    {
        $this->borrowRepository = $this->createMock(BorrowRepository::class);
        $this->borrowValidator = new BorrowValidator($this->borrowRepository);
    }

    public function testValidateBookCanBeBorrowedThrowsExceptionWhenArchived(): void
    {
        $book = $this->createMock(Book::class);
        $book->method('isArchived')->willReturn(true);

        $this->expectException(BookNotAvailableException::class);
        $this->expectExceptionMessage('Ce livre est archivé et ne peut pas être emprunté.');

        $this->borrowValidator->validateBookCanBeBorrowed($book);
    }

    public function testValidateBookCanBeBorrowedThrowsExceptionWhenActivelyBorrowed(): void
    {
        $book = $this->createMock(Book::class);
        $book->method('isArchived')->willReturn(false);

        $this->borrowRepository
            ->method('hasActiveBorrow')
            ->with($book)
            ->willReturn(true);

        $borrow = $this->createMock(\App\Entity\Borrow::class);
        $returnDate = new \DateTime('2025-12-31');
        $borrow->method('getReturnDueDate')->willReturn($returnDate);

        $this->borrowRepository
            ->method('findActiveBorrowForBook')
            ->with($book)
            ->willReturn($borrow);

        $this->expectException(BookNotAvailableException::class);
        $this->expectExceptionMessageMatches('/déjà emprunté/');

        $this->borrowValidator->validateBookCanBeBorrowed($book);
    }

    public function testValidateBookCanBeBorrowedSucceedsWhenAvailable(): void
    {
        $book = $this->createMock(Book::class);
        $book->method('isArchived')->willReturn(false);

        $this->borrowRepository
            ->method('hasActiveBorrow')
            ->with($book)
            ->willReturn(false);

        $this->borrowValidator->validateBookCanBeBorrowed($book);
        $this->assertTrue(true); // Assert test passed
    }

    public function testValidateUserCanBorrowThrowsExceptionWhenInactive(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isActive')->willReturn(false);

        $this->expectException(UserNotActiveException::class);
        $this->expectExceptionMessage('Votre compte est désactivé');

        $this->borrowValidator->validateUserCanBorrow($user);
    }

    public function testValidateUserCanBorrowThrowsExceptionWhenLimitExceeded(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isActive')->willReturn(true);

        $activeBorrows = array_fill(0, 5, $this->createMock(\App\Entity\Borrow::class));

        $this->borrowRepository
            ->method('findActiveBorrowsForUser')
            ->with($user)
            ->willReturn($activeBorrows);

        $this->expectException(BorrowLimitExceededException::class);
        $this->expectExceptionMessageMatches('/nombre maximum/');

        $this->borrowValidator->validateUserCanBorrow($user, 5);
    }

    public function testValidateUserCanBorrowSucceedsWhenValid(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isActive')->willReturn(true);

        $activeBorrows = array_fill(0, 2, $this->createMock(\App\Entity\Borrow::class));

        $this->borrowRepository
            ->method('findActiveBorrowsForUser')
            ->with($user)
            ->willReturn($activeBorrows);

        $this->borrowValidator->validateUserCanBorrow($user, 5);
        $this->assertTrue(true);
    }

    public function testValidateBorrowValidatesBothBookAndUser(): void
    {
        $book = $this->createMock(Book::class);
        $book->method('isArchived')->willReturn(false);

        $user = $this->createMock(User::class);
        $user->method('isActive')->willReturn(true);

        $this->borrowRepository
            ->method('hasActiveBorrow')
            ->willReturn(false);

        $this->borrowRepository
            ->method('findActiveBorrowsForUser')
            ->willReturn([]);

        $this->borrowValidator->validateBorrow($book, $user);
        $this->assertTrue(true);
    }
}
