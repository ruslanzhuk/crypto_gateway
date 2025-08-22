<?php

namespace App\Service;

use App\Entity\TelegramBotChat;
use App\Entity\TelegramBotIntegration;
use Doctrine\ORM\EntityManagerInterface;

class TelegramBotChatService
{
	public function __construct(private EntityManagerInterface $em)
	{
	}

	public function registerGroupChat(TelegramBotIntegration $telegramBotIntegration, string $chatId, string $username = null): void
	{

		error_log("Me in service");
		$chat = $this->em->getRepository(TelegramBotChat::class)->findOneBy(['chatId' => $chatId, 'integration' => $telegramBotIntegration]); //'$integration' => $telegramBotIntegration
		$a = is_string($chatId);
		error_log($chatId, $a);
		error_log("3");
		error_log($username);
		if (!$chat) {
			$chat = new TelegramBotChat();
			$chat->setChatId($chatId);
			error_log("Tu ok");
			$chat->setIntegration($telegramBotIntegration);
			$chat->setUsername($username);
			$chat->setIsVerified(true);
			$chat->setRole("ROLE_NONE");
			error_log("Tu ok 2");
			$this->em->persist($chat);
			error_log("Persist");
		} else {
			$chat->setIsVerified(true);
		}

		$this->em->flush();
	}

	public function setRole(TelegramBotIntegration $telegramBotIntegration, string $chatId, string $role): void
	{
		$chat = $this->em->getRepository(TelegramBotChat::class)->findOneBy(['chatId' => $chatId, 'integration' => $telegramBotIntegration]); //'$integration' => $telegramBotIntegration

		if (!$chat) {
			throw new \RuntimeException("❌ Чат $chatId не знайдено або не зареєстрований.");
		}

		if ($chat->getRole() !== 'ROLE_NONE') {
			throw new \RuntimeException("⚠️ Цей чат вже має роль {$chat->getRole()}.");
		}

		$chat->setRole($role);
		$this->em->flush();
	}

}