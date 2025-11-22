<?php

namespace App\Controller;

use App\Entity\BookCondition;
use App\Form\BookConditionType;
use App\Repository\BookConditionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/book/condition')]
#[IsGranted('ROLE_ADMIN')] // Seuls les admins peuvent gérer les états
final class BookConditionController extends AbstractController
{
    #[Route(name: 'app_book_condition_index', methods: ['GET'])]
    public function index(BookConditionRepository $bookConditionRepository): Response
    {
        return $this->render('book_condition/index.html.twig', [
            'book_conditions' => $bookConditionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_book_condition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bookCondition = new BookCondition();
        $form = $this->createForm(BookConditionType::class, $bookCondition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bookCondition);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_condition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book_condition/new.html.twig', [
            'book_condition' => $bookCondition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_condition_show', methods: ['GET'])]
    public function show(BookCondition $bookCondition): Response
    {
        return $this->render('book_condition/show.html.twig', [
            'book_condition' => $bookCondition,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_condition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BookCondition $bookCondition, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookConditionType::class, $bookCondition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_condition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book_condition/edit.html.twig', [
            'book_condition' => $bookCondition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_condition_delete', methods: ['POST'])]
    public function delete(Request $request, BookCondition $bookCondition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bookCondition->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bookCondition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_condition_index', [], Response::HTTP_SEE_OTHER);
    }
}
