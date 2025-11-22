<?php

namespace App\Controller;

use App\Service\DashboardStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(DashboardStatsService $statsService): Response
    {
        if ($this->isGranted('ROLE_LIBRARIAN')) {
            return $this->redirectToRoute('app_librarian_dashboard');
        }

        $user = $this->getUser();
        $stats = $statsService->getUserStats($user);

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}
