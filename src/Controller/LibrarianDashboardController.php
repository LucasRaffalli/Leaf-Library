<?php

namespace App\Controller;

use App\Service\DashboardStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/librarian')]
#[IsGranted('ROLE_LIBRARIAN')]
class LibrarianDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_librarian_dashboard')]
    public function index(DashboardStatsService $statsService): Response
    {
        $stats = $statsService->getLibrarianStats();

        return $this->render('librarian/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }
}
