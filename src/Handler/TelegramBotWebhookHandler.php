<?php

namespace App\Handler;

use App\Manager\TelegramBot\ConfirmationCodeManager;
use App\Repository\TelegramBotIntegrationRepository;
use App\Service\TelegramBotChatService;
use App\Service\TelegramBotService;

class TelegramBotWebhookHandler
{
	public function __construct(
		private readonly TelegramBotIntegrationRepository $botRepo,
		private readonly TelegramBotService $botService,
		private readonly ConfirmationCodeManager $codeManager,
		private readonly TelegramBotChatService $botChatService,
	)
	{
	}

	public function handle(array $payload, string $token): void
	{
		if (!isset($payload['message'])) {
			return;
		}

		$message = $payload['message'];
		$chatId = $message["chat"]["id"];
		$chatType = $message["chat"]["type"];
		$text = $message["text"] ?? null;
		$userId = $message["from"]["id"] ?? null;
		$chatTitle = $message["chat"]["title"] ?? null;

		$bot = $this->botRepo->findOneBy(['botToken' => $token]);
		if (!$bot) {
			return;
		}

		if ($chatType == 'group' && $text == "/register_confirm") {
			$code = $this->codeManager->generateCode($chatType, $chatId);

			if ($userId) {
				$this->botService->notifyUser($bot, $userId, "Ваш код для підтвердження групи: {$code}");
			}

			$this->botService->notifyUser($bot, $chatId, "✅ Код надіслано вам у приватний чат.");
			return;
		}

		if ($chatType == 'group' && $text == "/role_logger_chat") {
			try {
				$this->botChatService->setRole($bot, $chatId, 'ROLE_LOGGER_CHAT');
				$this->botService->notifyUser($bot, $chatId, "✅ Роль цього чату змінено на LOGGER_CHAT!");
			} catch (\RuntimeException $e) {
				$this->botService->notifyUser($bot, $chatId, $e->getMessage());
			}
			return;
		}

		if ($chatType == 'group' && !str_starts_with($text, '/')) {
			$this->botService->notifyUser($bot, $chatId, "I got ur message >▽<");
			return;
		}

		if ($chatType === 'private') {
			if ($bot->isVerified()) {
//				$this->botService->notifyUser($bot, $chatId, 'I got ur message');
				if($text && $groupChatId = $this->codeManager->findChatByCode('group', $text)) {
					$this->botChatService->registerGroupChat($bot, $groupChatId, $chatTitle);
					$this->botService->notifyUser($bot, $chatId, '✅ Групу підтверджено!');
					$this->botService->notifyUser($bot, $groupChatId, '✅ Цей чат підтверджено!');
				} else if($text && $this->codeManager->findKeysByPrefix("confirm_code_")) {
					$this->botService->notifyUser($bot, $chatId, '❌ Невірний код!');
				}
				return;
			} else {
				$botId = explode(':', $bot->getBotToken())[0];
				if ($text && $this->codeManager->isValidCode($chatType, $botId, $text)) {
					$this->botService->confirmBot($bot);

					$this->botService->notifyUser($bot, $chatId, '✅ Code confirmed!');
					$this->botService->notifyUser($bot, $chatId, 'Welcome to our team CRYPTOGreenmanageR! This bot will help You to monitor every transaction that will come to Yours wallets and it will inform You if money (so sweet) have come!🤝');
				} else {
					$this->botService->notifyUser($bot, $chatId, '❌ Code not confirmed!');
				}
			}
		}
	}
}