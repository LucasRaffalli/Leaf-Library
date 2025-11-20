<?php

namespace App\Service;

use App\Repository\BookConditionRepository;
use App\Repository\BookRepository;
use App\Repository\BookStatusRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;

class DashboardStatsService
{
    public function __construct(
        private BookRepository $bookRepository,
        private UserRepository $userRepository,
        private CategoryRepository $categoryRepository,
        private BookStatusRepository $statusRepository,
        private BookConditionRepository $conditionRepository
    ) {
    }

    public function getStats(): array
    {
        return [
            'totalBooks' => $this->bookRepository->count([]),
            'availableBooks' => $this->bookRepository->count(['status' => $this->statusRepository->findOneBy(['name' => 'Disponible'])]),
            'borrowedBooks' => $this->bookRepository->count(['status' => $this->statusRepository->findOneBy(['name' => 'Emprunté'])]),
            'archivedBooks' => $this->bookRepository->count(['isArchived' => true]),
            'totalUsers' => $this->userRepository->count([]),
            'activeUsers' => $this->userRepository->count(['isActive' => true]),
            'totalCategories' => $this->categoryRepository->count([]),
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
}
