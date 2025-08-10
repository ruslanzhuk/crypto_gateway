<?php

namespace App\Controller;

use App\Repository\TelegramBotIntegrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route(name: 'app_dashboard')]
    public function index(TelegramBotIntegrationRepository $botRepo): Response
    {
		$user = $this->getUser();
		$botsCount = $user ? $botRepo->count(['creator' => $user]) : 0;

        return $this->render('dashboard/dashboard_main.html.twig', [
	        'botsCount' => $botsCount,
        ]);
    }
}
