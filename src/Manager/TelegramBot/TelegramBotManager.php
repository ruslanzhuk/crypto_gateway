<?php

namespace App\Manager\TelegramBot;

use App\Factory\TelegramBotClientFactory;

class TelegramBotManager
{

	public function __construct(
		private readonly TelegramBotClientFactory $clientFactory,
		private readonly string $webhookBaseUrl,
	)
	{
	}

	public function getBotInfo(string $token): array {
		$client = $this->clientFactory->create($token);
		return $client->getBotInfo();
	}

	public function setWebhook(string $token): void {
		$webhookUrl = $this->webhookBaseUrl . "?token=" . $token;
		$client = $this->clientFactory->create($token);
		$client->setWebhook($webhookUrl);
	}

	public function sendMessage(string $token, int $chatId, string $message): void {
		$client = $this->clientFactory->create($token);
		$client->sendMessage($chatId, $message);
	}

}