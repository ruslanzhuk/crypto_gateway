<?php

namespace App\Integrations\TelegramBot;

use Psr\Log\LoggerInterface;

class TelegramBotClient
{
	private const SEND_MESSAGE_ENDPOINT = "sendMessage";
	private const GET_ME_ENDPOINT = "getMe";

	public function __construct(
		private readonly TelegramBotConnector $connector,
		private readonly string $botToken,
		private readonly LoggerInterface $logger,
	)
	{
//		$this->setWebhook();
	}

	public function setWebhook(string $webhookUrl): void
	{
		$response = $this->connector->post($this->botToken, 'setWebhook', [
			'url' => $webhookUrl,
		]);

		if ($response->getStatusCode() === 200) {
			$data = json_decode($response->getContent(), true);
			if ($data["ok"]) {
				$this->logger->info("Webhook for bot token {$this->botToken} set successfully.");
			} else {
				$this->logger->info("Failed to set webhook for bot token {$this->botToken}: {$data['description']}.");
			}
		}
	}

	public function getBotInfo(): array
	{
		$response = $this->connector->get($this->botToken, self::GET_ME_ENDPOINT);
		return json_decode($response->getContent(), true)["result"];
	}

	public function sendMessage(int $chatId, string $message): void {
		 $this->connector->post($this->botToken, self::SEND_MESSAGE_ENDPOINT, [
			 'chat_id' => $chatId,
			 'text' => $message,
		 ]);
	}

//	public function monitorWallet(int $chatId, string $walletAddress, float $lastBalance = 0.0): void {
//		$currentBalance = $this->checkWalletBalance($walletAddress);
//
//		if ($currentBalance > $lastBalance) {
//			$difference = $currentBalance - $lastBalance;
//			$message = "New funds detected! Wallet $walletAddress received $difference USD.";
//			$this->sendMessage($chatId, $message);
//		}
//
//		$lastBalance = $currentBalance;
//	}
//
//	private function checkWalletBalance(string $walletAddress): string {}
}