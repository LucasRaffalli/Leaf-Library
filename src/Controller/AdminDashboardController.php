<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use App\Repository\BookStatusRepository;
use App\Repository\BookConditionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        BookRepository $bookRepository,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        BookStatusRepository $statusRepository,
        BookConditionRepository $conditionRepository
    ): Response {
        $totalBooks = $bookRepository->count([]);
        $availableBooks = $bookRepository->count(['status' => $statusRepository->findOneBy(['name' => 'Disponible'])]);
        $borrowedBooks = $bookRepository->count(['status' => $statusRepository->findOneBy(['name' => 'Emprunté'])]);
        $archivedBooks = $bookRepository->count(['isArchived' => true]);
        
        $totalUsers = $userRepository->count([]);
        $activeUsers = $userRepository->count(['isActive' => true]);
        $totalCategories = $categoryRepository->count([]);

  
        $recentBooks = $bookRepository->findBy([], ['createdAt' => 'DESC'], 5);
        

        $recentUsers = $userRepository->findBy([], ['createdAt' => 'DESC'], 5);

        $categories = $categoryRepository->findAll();
        $booksByCategory = [];
        foreach ($categories as $category) {
            $booksByCategory[$category->getName()] = count($category->getBooks());
        }

        $conditions = $conditionRepository->findAll();
        $booksByCondition = [];
        foreach ($conditions as $condition) {
            $booksByCondition[$condition->getName()] = count($condition->getBooks());
        }

        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'totalBooks' => $totalBooks,
                'availableBooks' => $availableBooks,
                'borrowedBooks' => $borrowedBooks,
                'archivedBooks' => $archivedBooks,
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'totalCategories' => $totalCategories,
            ],
            'recentBooks' => $recentBooks,
            'recentUsers' => $recentUsers,
            'booksByCategory' => $booksByCategory,
            'booksByCondition' => $booksByCondition,
        ]);
    }
}