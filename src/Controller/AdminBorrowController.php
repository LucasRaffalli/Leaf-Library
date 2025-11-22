<?php

namespace App\Controller;

use App\Entity\Borrow;
use App\Repository\BorrowRepository;
use App\Repository\BorrowStatusRepository;
use App\Repository\BookConditionRepository;
use App\Service\BookStatusService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/emprunts')]
#[IsGranted('ROLE_LIBRARIAN')] // Bibliothécaires peuvent gérer les retours
class AdminBorrowController extends AbstractController
{
    #[Route('/', name: 'app_admin_borrow_index')]
    public function index(BorrowRepository $borrowRepository): Response
    {
        $pendingVerification = $borrowRepository->createQueryBuilder('b')
            ->join('b.status', 's')
            ->where('s.name = :status')
            ->setParameter('status', 'En vérification')
            ->orderBy('b.returnDate', 'ASC')
            ->getQuery()
            ->getResult();

        $allBorrows = $borrowRepository->findBy([], ['borrowDate' => 'DESC'], 50);

        return $this->render('admin/borrow/index.html.twig', [
            'pendingVerification' => $pendingVerification,
            'allBorrows' => $allBorrows,
        ]);
    }

    #[Route('/verifier/{id}', name: 'app_admin_borrow_verify')]
    public function verify(Borrow $borrow, BookConditionRepository $conditionRepository): Response
    {
        // Vérifier que le statut est "En vérification"
        if ($borrow->getStatus()->getName() !== 'En vérification') {
            $this->addFlash('error', 'Cet emprunt n\'est pas en attente de vérification.');
            return $this->redirectToRoute('app_admin_borrow_index');
        }

        $conditions = $conditionRepository->findAll();

        return $this->render('admin/borrow/verify.html.twig', [
            'borrow' => $borrow,
            'conditions' => $conditions,
        ]);
    }

    #[Route('/valider/{id}', name: 'app_admin_borrow_validate', methods: ['POST'])]
    public function validate(
        Borrow $borrow,
        Request $request,
        EntityManagerInterface $entityManager,
        BorrowStatusRepository $statusRepository,
        BookConditionRepository $conditionRepository,
        BookStatusService $bookStatusService
    ): Response {
        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('validate-borrow-' . $borrow->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_admin_borrow_index');
        }

        // Récupérer les données du formulaire
        $returnedConditionId = $request->request->get('returned_condition');
        $cautionAction = $request->request->get('caution_action'); // 'returned', 'kept', 'additional'
        $additionalFee = (float) $request->request->get('additional_fee', 0);
        $notes = $request->request->get('notes', '');

        // Mettre à jour l'état retourné du livre ET l'état actuel du livre
        $returnedCondition = $conditionRepository->find($returnedConditionId);
        if ($returnedCondition) {
            $borrow->setReturnedCondition($returnedCondition->getName());
            // Mettre à jour l'état du livre dans la base
            $borrow->getBook()->setCondition($returnedCondition);
        }

        // Logique de caution améliorée
        if ($borrow->hasDeposit()) {
            if ($cautionAction === 'returned') {
                // Caution restituée intégralement
                $borrow->setIsDepositReturned(true);
                $borrow->setAdditionalFees(null);
            } elseif ($cautionAction === 'kept') {
                // Caution conservée (livre abîmé)
                $borrow->setIsDepositReturned(false);
                $borrow->setAdditionalFees(null);
            } elseif ($cautionAction === 'additional') {
                // Caution conservée + frais supplémentaires
                $borrow->setIsDepositReturned(false);
                $borrow->setAdditionalFees((string) $additionalFee);
            }
        } else {
            // Pas de caution mais peut-être des frais
            if ($additionalFee > 0) {
                $borrow->setAdditionalFees((string) $additionalFee);
            }
            $borrow->setIsDepositReturned(true); // N/A
        }

        // Sauvegarder les notes
        if (!empty($notes)) {
            $borrow->setAdminNotes($notes);
        }

        // Marquer comme "Rendu"
        $status = $statusRepository->findOneBy(['name' => 'Rendu']);
        $borrow->setStatus($status);
        $borrow->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        // Mettre à jour le statut du livre à "Disponible"
        $bookStatusService->updateStatusWhenReturned($borrow->getBook());

        // Message personnalisé
        $message = 'Emprunt validé avec succès.';
        if ($borrow->hasDeposit()) {
            if ($cautionAction === 'returned') {
                $message .= ' Caution restituée.';
            } elseif ($cautionAction === 'kept') {
                $message .= ' Caution conservée (' . $borrow->getDepositAmount() . '€).';
            } elseif ($cautionAction === 'additional') {
                $total = (float)$borrow->getDepositAmount() + $additionalFee;
                $message .= sprintf(' Caution conservée + frais supplémentaires (Total: %.2f€).', $total);
            }
        } elseif ($additionalFee > 0) {
            $message .= sprintf(' Frais facturés : %.2f€', $additionalFee);
        }

        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_admin_borrow_index');
    }
}
