<?php

namespace App\Service;

use App\Repository\BookConditionRepository;
use App\Repository\BookRepository;
use App\Repository\BookStatusRepository;
use App\Repository\BorrowRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;

class DashboardStatsService
{
    public function __construct(
        private BookRepository $bookRepository,
        private UserRepository $userRepository,
        private CategoryRepository $categoryRepository,
        private BookStatusRepository $statusRepository,
        private BookConditionRepository $conditionRepository,
        private BorrowRepository $borrowRepository
    ) {
    }

    public function getStats(): array
    {
        $activeBorrows = $this->borrowRepository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->join('b.status', 's')
            ->where('s.name IN (:statuses)')
            ->setParameter('statuses', ['En cours', 'En retard'])
            ->getQuery()
            ->getSingleScalarResult();

        $overdueBorrows = $this->borrowRepository->findOverdueBorrows();

        return [
            'totalBooks' => $this->bookRepository->count([]),
            'availableBooks' => $this->bookRepository->count(['status' => $this->statusRepository->findOneBy(['name' => 'Disponible'])]),
            'borrowedBooks' => $this->bookRepository->count(['status' => $this->statusRepository->findOneBy(['name' => 'Emprunté'])]),
            'archivedBooks' => $this->bookRepository->count(['isArchived' => true]),
            'totalUsers' => $this->userRepository->count([]),
            'activeUsers' => $this->userRepository->count(['isActive' => true]),
            'totalCategories' => $this->categoryRepository->count([]),
            'activeBorrows' => $activeBorrows,
            'overdueBorrows' => count($overdueBorrows),
        ];
    }

    public function getUserStats($user): array
    {
        $activeBorrows = $this->borrowRepository->findActiveBorrowsForUser($user);
        $overdueBorrows = $this->borrowRepository->findOverdueBorrows($user);

        return [
            'user' => $user,
            'activeBorrows' => $activeBorrows,
            'activeBorrowsCount' => count($activeBorrows),
            'overdueBorrowsCount' => count($overdueBorrows),
        ];
    }

    public function getRecentBooks(int $limit = 5): array
    {
        return $this->bookRepository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    public function getRecentUsers(int $limit = 5): array
    {
        return $this->userRepository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    public function getBooksByCategory(): array
    {
        $categories = $this->categoryRepository->findAll();
        $stats = [];
        foreach ($categories as $category) {
            $stats[$category->getName()] = count($category->getBooks());
        }
        return $stats;
    }

    public function getBooksByCondition(): array
    {
        $conditions = $this->conditionRepository->findAll();
        $stats = [];
        foreach ($conditions as $condition) {
            $stats[$condition->getName()] = count($condition->getBooks());
        }
        return $stats;
    }

    public function getRecentBorrows(int $limit = 5): array
    {
        return $this->borrowRepository->findBy([], ['borrowDate' => 'DESC'], $limit);
    }

    public function getOverdueBorrows(): array
    {
        return $this->borrowRepository->findOverdueBorrows();
    }


    public function getLibrarianStats(): array
    {
        $activeBorrows = $this->borrowRepository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->join('b.status', 's')
            ->where('s.name = :status')
            ->setParameter('status', 'En cours')
            ->getQuery()
            ->getSingleScalarResult();

        $lateBorrows = $this->borrowRepository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->join('b.status', 's')
            ->where('s.name = :status')
            ->setParameter('status', 'En retard')
            ->getQuery()
            ->getSingleScalarResult();

        $pendingVerification = $this->borrowRepository->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->join('b.status', 's')
            ->where('s.name = :status')
            ->setParameter('status', 'En vérification')
            ->getQuery()
            ->getSingleScalarResult();

        $borrowsToValidate = $this->borrowRepository->createQueryBuilder('b')
            ->join('b.status', 's')
            ->join('b.book', 'book')
            ->join('b.user', 'user')
            ->where('s.name = :status')
            ->setParameter('status', 'En vérification')
            ->orderBy('b.returnDate', 'ASC')
            ->getQuery()
            ->getResult();

        $availableBooks = $this->bookRepository->count([
            'status' => $this->statusRepository->findOneBy(['name' => 'Disponible'])
        ]);

        $borrowedBooks = $this->bookRepository->count([
            'status' => $this->statusRepository->findOneBy(['name' => 'Emprunté'])
        ]);

        $totalBooks = $this->bookRepository->count([]);

        $totalUsers = $this->userRepository->count(['isActive' => true]);

        return [
            'activeBorrows' => (int) $activeBorrows,
            'lateBorrows' => (int) $lateBorrows,
            'pendingVerification' => (int) $pendingVerification,
            'borrowsToValidate' => $borrowsToValidate,
            'availableBooks' => $availableBooks,
            'borrowedBooks' => $borrowedBooks,
            'totalBooks' => $totalBooks,
            'totalUsers' => $totalUsers,
        ];
    }
}

