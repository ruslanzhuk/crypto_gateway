<?php

namespace App\Controller;

use App\Handler\TelegramBotWebhookHandler;
use App\Manager\TelegramBot\ConfirmationCodeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class WebhookController extends AbstractController
{
	#[Route('/telegram/webhook', name: 'telegram_webhook', methods: ['POST'])]
	public function __invoke(Request $request, TelegramBotWebhookHandler $handler): Response
	{
		$token = $request->query->get('token');

		$payload = json_decode($request->getContent(), true);

		$handler->handle($payload, $token);

		return new Response('OK');
	}
}
