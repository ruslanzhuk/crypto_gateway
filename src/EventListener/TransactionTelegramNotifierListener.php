<?php

namespace App\EventListener;

use App\Entity\TelegramBotChat;
use App\Entity\TelegramBotIntegration;
use App\Event\TransactionCreatedEvent;
use App\Service\TelegramBotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;


#[AsEventListener(event: TransactionCreatedEvent::NAME, priority: -10)]
class TransactionTelegramNotifierListener
{
	public function __construct(
		private TelegramBotService $telegramBotService,
		private EntityManagerInterface $em,
	)
	{
	}

	public function __invoke(TransactionCreatedEvent $event): void
	{
		$transaction = $event->getTransaction();
		$user = $transaction->getUser();

		$telegramIntegration = $this->em->getRepository(TelegramBotIntegration::class)->findOneBy(['creator' => $user]);
		if (!$telegramIntegration) {
			return;
		}

		$loggerChats = $this->em->getRepository(TelegramBotChat::class)->findBy(['integration' => $telegramIntegration, 'role' => 'ROLE_LOGGER_CHAT', 'isVerified' => true]);

		foreach ($loggerChats as $chat) {
			$chatId = $chat->getChatId();
			$this->telegramBotService->notifyUser($telegramIntegration, $chatId,
				"🆕 Нова транзакція створена!\nСума: {$transaction->getAmountCrypto()} {$transaction->getCryptoCurrency()->getName()}\nКористувач: {$transaction->getUser()->getEmail()}\nTxHash: {$transaction->getTxHash()}"
			);
		}
	}
}