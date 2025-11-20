<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\BookStatus;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\BookStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue', methods: ['GET'])]
    #[Route('/books', name: 'app_books', methods: ['GET'])]
    public function index(
        Request $request, 
        BookRepository $bookRepository,
        CategoryRepository $categoryRepository,
        BookStatusRepository $bookStatusRepository
    ): Response {
        $search = $request->query->get('search', '');
        $categoryId = $request->query->get('category', '');
        $statusId = $request->query->get('status', '');

        $queryBuilder = $bookRepository->createQueryBuilder('b')
            ->leftJoin('b.categories', 'c')
            ->leftJoin('b.status', 's')
            ->leftJoin('b.condition', 'cond')
            ->where('b.isArchived = :archived')
            ->setParameter('archived', false);

        if (!empty($search)) {
            $queryBuilder->andWhere('b.title LIKE :search OR b.author LIKE :search')
                         ->setParameter('search', '%' . $search . '%');
        }

        if (!empty($categoryId)) {
            $queryBuilder->andWhere('c.id = :categoryId')
                         ->setParameter('categoryId', $categoryId);
        }

        if (!empty($statusId)) {
            $queryBuilder->andWhere('s.id = :statusId')
                         ->setParameter('statusId', $statusId);
        }

        $books = $queryBuilder->getQuery()->getResult();

        $categories = $categoryRepository->findAll();
        $statuses = $bookStatusRepository->findAll();

        return $this->render('catalogue/index.html.twig', [
            'books' => $books,
            'categories' => $categories,
            'statuses' => $statuses,
            'current_search' => $search,
            'current_category' => $categoryId,
            'current_status' => $statusId,
        ]);
    }

    #[Route('/catalogue/{id}', name: 'app_catalogue_show', methods: ['GET'])]
    #[Route('/books/{id}', name: 'app_books_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('catalogue/show.html.twig', [
            'book' => $book,
        ]);
    }
}