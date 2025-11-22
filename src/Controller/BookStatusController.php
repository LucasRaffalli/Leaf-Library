<?php

namespace App\Controller;

use App\Entity\BookStatus;
use App\Form\BookStatusType;
use App\Repository\BookStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/book/status')]
#[IsGranted('ROLE_ADMIN')] // Seuls les admins peuvent gérer les statuts
final class BookStatusController extends AbstractController
{
    #[Route(name: 'app_book_status_index', methods: ['GET'])]
    public function index(BookStatusRepository $bookStatusRepository): Response
    {
        return $this->render('book_status/index.html.twig', [
            'book_statuses' => $bookStatusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_book_status_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bookStatus = new BookStatus();
        $form = $this->createForm(BookStatusType::class, $bookStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bookStatus);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book_status/new.html.twig', [
            'book_status' => $bookStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_status_show', methods: ['GET'])]
    public function show(BookStatus $bookStatus): Response
    {
        return $this->render('book_status/show.html.twig', [
            'book_status' => $bookStatus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_status_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BookStatus $bookStatus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookStatusType::class, $bookStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book_status/edit.html.twig', [
            'book_status' => $bookStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_status_delete', methods: ['POST'])]
    public function delete(Request $request, BookStatus $bookStatus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bookStatus->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bookStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_status_index', [], Response::HTTP_SEE_OTHER);
    }
}
