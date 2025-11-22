<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Repository\BorrowRepository;
use App\Service\BookAvailabilityService;
use PHPUnit\Framework\TestCase;

class BookAvailabilityServiceTest extends TestCase
{
    private BorrowRepository $borrowRepository;
    private BookAvailabilityService $bookAvailabilityService;

    protected function setUp(): void
    {
        $this->borrowRepository = $this->createMock(BorrowRepository::class);
        $this->bookAvailabilityService = new BookAvailabilityService($this->borrowRepository);
    }

    public function testHasActiveBorrowReturnsTrueWhenActiveExists(): void
    {
        $book = $this->createMock(Book::class);

        $this->borrowRepository
            ->expects($this->once())
            ->method('hasActiveBorrow')
            ->with($book)
            ->willReturn(true);

        $result = $this->bookAvailabilityService->hasActiveBorrow($book);

        $this->assertTrue($result);
    }

    public function testHasActiveBorrowReturnsFalseWhenNoActive(): void
    {
        $book = $this->createMock(Book::class);

        $this->borrowRepository
            ->expects($this->once())
            ->method('hasActiveBorrow')
            ->with($book)
            ->willReturn(false);

        $result = $this->bookAvailabilityService->hasActiveBorrow($book);

        $this->assertFalse($result);
    }

    public function testGetActiveBorrowReturnsActiveBorrow(): void
    {
        $book = $this->createMock(Book::class);
        $borrow = $this->createMock(\App\Entity\Borrow::class);

        $this->borrowRepository
            ->expects($this->once())
            ->method('findActiveBorrowForBook')
            ->with($book)
            ->willReturn($borrow);

        $result = $this->bookAvailabilityService->getActiveBorrow($book);

        $this->assertSame($borrow, $result);
    }

    public function testGetActiveBorrowReturnsNullWhenNoActive(): void
    {
        $book = $this->createMock(Book::class);

        $this->borrowRepository
            ->expects($this->once())
            ->method('findActiveBorrowForBook')
            ->with($book)
            ->willReturn(null);

        $result = $this->bookAvailabilityService->getActiveBorrow($book);

        $this->assertNull($result);
    }

    public function testIsAvailableForBorrowReturnsTrueWhenNotArchivedAndNoActiveBorrow(): void
    {
        $book = $this->createMock(Book::class);
        $book->method('isArchived')->willReturn(false);

        $this->borrowRepository
            ->method('hasActiveBorrow')
            ->with($book)
            ->willReturn(false);

        $result = $this->bookAvailabilityService->isAvailableForBorrow($book);

        $this->assertTrue($result);
    }

    public function testIsAvailableForBorrowReturnsFalseWhenArchived(): void
    {
        $book = $this->createMock(Book::class);
        $book->method('isArchived')->willReturn(true);

        $result = $this->bookAvailabilityService->isAvailableForBorrow($book);

        $this->assertFalse($result);
    }

    public function testIsAvailableForBorrowReturnsFalseWhenHasActiveBorrow(): void
    {
        $book = $this->createMock(Book::class);
        $book->method('isArchived')->willReturn(false);

        $this->borrowRepository
            ->method('hasActiveBorrow')
            ->with($book)
            ->willReturn(true);

        $result = $this->bookAvailabilityService->isAvailableForBorrow($book);

        $this->assertFalse($result);
    }
}
