<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Borrow;
use App\Form\BorrowType;
use App\Repository\BorrowRepository;
use App\Repository\BorrowStatusRepository;
use App\Service\BookStatusService;
use App\Service\BorrowValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/emprunts')]
#[IsGranted('ROLE_USER')]
class BorrowController extends AbstractController
{
    #[Route('/', name: 'app_borrow_index')]
    public function index(BorrowRepository $borrowRepository): Response
    {
        $user = $this->getUser();
        $activeBorrows = $borrowRepository->findActiveBorrowsForUser($user);
        $allBorrows = $borrowRepository->findBy(['user' => $user], ['borrowDate' => 'DESC']);

        return $this->render('borrow/index.html.twig', [
            'activeBorrows' => $activeBorrows,
            'allBorrows' => $allBorrows,
        ]);
    }

    #[Route('/emprunter/{id}', name: 'app_borrow_create')]
    public function create(
        Book $book,
        Request $request,
        EntityManagerInterface $entityManager,
        BorrowValidator $borrowValidator,
        BorrowStatusRepository $statusRepository,
        BookStatusService $bookStatusService
    ): Response {
        $borrowValidator->validateBorrow($book, $this->getUser());

        $borrow = new Borrow();
        $form = $this->createForm(BorrowType::class, $borrow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $statusRepository->findOneBy(['name' => 'En cours']);
            
            $borrow->setUser($this->getUser());
            $borrow->setBook($book);
            $borrow->setStatus($status);
            $borrow->setBorrowDate(new \DateTime());
            $borrow->setHasDeposit($book->getDepositAmount() > 0);
            $borrow->setDepositAmount($book->getDepositAmount());
            $borrow->setIsDepositReturned(false);
            $borrow->setCreatedAt(new \DateTimeImmutable());
            $borrow->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($borrow);
            $entityManager->flush();

            $bookStatusService->updateStatusWhenBorrowed($book);

            $this->addFlash('success', 'Le livre a été emprunté avec succès !');
            return $this->redirectToRoute('app_borrow_index');
        }

        return $this->render('borrow/create.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/retourner/{id}', name: 'app_borrow_return', methods: ['POST'])]
    public function return(
        Borrow $borrow,
        EntityManagerInterface $entityManager,
        BorrowStatusRepository $statusRepository,
        Request $request,
        BookStatusService $bookStatusService
    ): Response {
        if (!$this->isCsrfTokenValid('return-borrow-' . $borrow->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_borrow_index');
        }

        if ($borrow->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez retourner que vos propres emprunts.');
            return $this->redirectToRoute('app_borrow_index');
        }

        if (in_array($borrow->getStatus()->getName(), ['Rendu', 'En vérification'])) {
            $this->addFlash('error', 'Ce livre a déjà été retourné.');
            return $this->redirectToRoute('app_borrow_index');
        }

        $status = $statusRepository->findOneBy(['name' => 'En vérification']);
        $borrow->setStatus($status);
        $borrow->setReturnDate(new \DateTime());
        $borrow->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        $this->addFlash('success', 'Le livre a été retourné ! Un administrateur va vérifier son état.');
        return $this->redirectToRoute('app_borrow_index');
    }

    #[Route('/{id}', name: 'app_borrow_show')]
    public function show(Borrow $borrow): Response
    {
        if ($borrow->getUser() !== $this->getUser() && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('borrow/show.html.twig', [
            'borrow' => $borrow,
        ]);
    }
}
