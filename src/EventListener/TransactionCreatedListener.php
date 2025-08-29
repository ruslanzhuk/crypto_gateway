<?php

namespace App\EventListener;

use App\Event\TransactionCreatedEvent;
use App\Service\FiatConversionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;


#[AsEventListener(event: TransactionCreatedEvent::NAME, priority: 10)]
class TransactionCreatedListener
{

	public function __construct(
		private FiatConversionService $fiatConversionService,
		private EntityManagerInterface $em,
	)
	{
	}

	public function __invoke(TransactionCreatedEvent $event): void
	{
		$transaction = $event->getTransaction();

		$this->fiatConversionService->createConversions(
			$transaction,
			$transaction->getAmountCrypto(),
			$transaction->getCryptoCurrency()->getName(),
		);
	}
}