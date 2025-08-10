<?php

namespace App\Service;

use App\Entity\TelegramBotIntegration;
use App\Manager\TelegramBot\TelegramBotManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TelegramBotService
{
	public function __construct(
		private readonly TelegramBotManager $manager,
		private readonly EntityManagerInterface $em,
		private readonly Security $security,
	) {}

	public function validateAndSave(string $token): TelegramBotIntegration
	{
		$botInfo = $this->manager->getBotInfo($token);

		$user = $this->security->getUser();

		if (!$user instanceof \App\Entity\User) {
			throw new \LogicException('Authenticated user is not valid.');
		}

		$bot = new TelegramBotIntegration();
		$bot->setBotToken($token);
		$bot->setBotName($botInfo['first_name']);
		$bot->setBotUsername($botInfo['username']);
		$bot->setCreator($user);
		$bot->setCreatedAt(new \DateTimeImmutable());
		$bot->setIsActive(false);
		$bot->setIsVerified(false);

		$this->em->persist($bot);
		$this->em->flush();

		$this->manager->setWebhook($token);

		return $bot;
	}

	public function notifyUser(TelegramBotIntegration $integration, int $chatId, string $message): void
	{
		$this->manager->sendMessage($integration->getBotToken(), $chatId, $message);
	}

	public function confirmBot(TelegramBotIntegration $bot): void
	{
		if (!$bot->isVerified()) {
			$bot->setIsVerified(true);
			$this->em->flush();
		}
	}

	public function activateIfVerified(TelegramBotIntegration $bot): bool
	{
		if ($bot->isVerified()) {
			$bot->setIsActive(true);
			$this->em->flush();

			return true;
		}

		return false;
	}
}