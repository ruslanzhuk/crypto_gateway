<?php

namespace App\Controller;

use App\Entity\TelegramBotIntegration;
use App\Form\TelegramBotIntegrationType;
use App\Manager\TelegramBot\ConfirmationCodeManager;
use App\Repository\TelegramBotIntegrationRepository;
use App\Service\TelegramBotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/botmanager')]
final class TelegramBotIntegrationController extends AbstractController
{
	public function __construct(private readonly TelegramBotService $telegramBotService, private readonly EntityManagerInterface $em)
	{
	}

	#[Route(name: 'app_bot_manager', methods: ['GET'])]
    public function index(TelegramBotIntegrationRepository $botRepo): Response
    {
        $user = $this->getUser();
        $bots = $botRepo->findBy(['creator' => $user], ['isActive' => 'DESC', 'createdAt' => 'DESC']);

        return $this->render('dashboard/telegram_bot/index.html.twig', [
            'bots' => $bots,
        ]);
    }

    #[Route('/new/info', name: 'app_bot_manager_new_info', methods: ['GET', 'POST'])]
    public function new_info(): Response
    {
        return $this->render('dashboard/telegram_bot/new_info.html.twig');
    }

    #[Route('/new/token', name: 'app_bot_manager_new_token', methods: ['GET', 'POST'])]
    public function new_token(Request $request, TelegramBotService $telegramBotService, ConfirmationCodeManager $codeManager): Response
    {
        $form = $this->createForm(TelegramBotIntegrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $token = $form->get('botToken')->getData();

                $botInfo = $telegramBotService->validateAndSave($token);

	            $botId = explode(":", $botInfo->getBotToken())[0];

				$codeManager->generateCode($botId);

                return $this->redirectToRoute('app_bot_manager_new_confirm', [
                    'id' => $botInfo->getId()
                ]);
            } catch (\Exception $e) {
                $this->addFlash("error", "No valid token or bot isn't exist." . $e->getMessage());
            }
        }

        return $this->render('dashboard/telegram_bot/new_token.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/new/confirm/{id}', name: 'app_bot_manager_new_confirm', methods: ['GET', 'POST'])]
    public function new_confirm(TelegramBotIntegration $bot, Request $request, ConfirmationCodeManager $codeManager): Response
    {
		if (!$bot) {
			throw $this->createNotFoundException('Unable to find bot.');
		}

		$botId = explode(":", $bot->getBotToken())[0];

		$code = $codeManager->getCode($botId);
	    $ttl = $codeManager->getCodeTTL($botId);
	    if ($request->isMethod('POST')) {
			if ($this->telegramBotService->activateIfVerified($bot)) {
				$this->addFlash('success', 'Бот успішно підтверджено і тепер активний.');

				return $this->redirectToRoute('app_bot_manager');
			} else {
				$this->addFlash('error', 'Бот ще не підтверджено. Спробуйте пізніше.');
				return $this->redirectToRoute('app_bot_manager_new_confirm', ['id' => $bot->getId()]);
			}
	    }

		return $this->render('dashboard/telegram_bot/new_confirm.html.twig', [
			'bot' => $bot,
			'confirmationCode' => $code,
			'ttl' => $ttl,
		]);
    }

	#[Route('/new/confirm/{id}/refresh', name: 'app_bot_manager_new_confirm_refresh', methods: ['POST'])]
	public function refreshCode(TelegramBotIntegration $bot, ConfirmationCodeManager $codeManager): Response
	{
		if (!$bot) {
			throw $this->createNotFoundException('Unable to find bot.');
		}

		$botId = explode(":", $bot->getBotToken())[0];

		$codeManager->generateCode($botId);

		$this->addFlash('success', 'Код успішно оновлено!');

		return $this->redirectToRoute('app_bot_manager_new_confirm', ['id' => $bot->getId()]);
	}

    #[Route('show/{id}', name: 'app_bot_manager_show', methods: ['GET'])]
    public function show(TelegramBotIntegration $telegramBotIntegration): Response
    {
        return $this->render('dashboard/telegram_bot/show.html.twig', [
            'telegram_bot_integration' => $telegramBotIntegration,
	        'chats' => $telegramBotIntegration->getTelegramBotChats()
        ]);
    }

    #[Route('/edit/{id}', name: 'app_bot_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TelegramBotIntegration $telegramBotIntegration, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TelegramBotIntegrationType::class, $telegramBotIntegration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_bot_manager', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/telegram_bot/edit.html.twig', [
            'telegram_bot_integration' => $telegramBotIntegration,
            'form' => $form,
        ]);
    }

    #[Route('delete/{id}', name: 'app_bot_manager_delete', methods: ['POST'])]
    public function delete(Request $request, TelegramBotIntegration $telegramBotIntegration, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$telegramBotIntegration->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($telegramBotIntegration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_bot_manager', [], Response::HTTP_SEE_OTHER);
    }
}
