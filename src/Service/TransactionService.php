<?php

namespace App\Service;

use App\Dtos\CreateTransactionPayload;
use App\Entity\FiatAmountPerTransaction;
use App\Entity\PaymentConfirmation;
use App\Entity\TelegramBotChat;
use App\Entity\TelegramBotIntegration;
use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Entity\PaymentStatus;
use App\Event\TransactionCreatedEvent;
use App\Service\Provider\Price\CoinGeckoPriceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TransactionService
{
    private EntityManagerInterface $em;
    private ParameterBagInterface $params;
    private CoinGeckoPriceProvider $priceProvider;
	private FiatConversionService $fiatConversionService;

	private TelegramBotService $telegramBotService;

	private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, CoinGeckoPriceProvider $priceProvider, FiatConversionService $fiatConversionService, TelegramBotService $telegramBotService, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->params = $params;
        $this->priceProvider = $priceProvider;
		$this->fiatConversionService = $fiatConversionService;
		$this->telegramBotService = $telegramBotService;
		$this->eventDispatcher = $eventDispatcher;
    }

    public function createTransaction(CreateTransactionPayload $payload, WalletService $walletService): Transaction
    {

        $now = new \DateTimeImmutable();

        $wallet = $walletService->createWallet($payload->network, $payload->user);

        $status = $this->em->getRepository(PaymentStatus::class)->findOneBy(['code' => 'PND']);
        if (!$status) {
            throw new \RuntimeException("Status 'PND' not be found in table payment_status.");
        }

        $tx = (new Transaction())
            ->setUser($payload->user)
            ->setWallet($wallet)
            ->setCryptoCurrency($payload->cryptoCurrency)
            ->setMainStatus($status)
            ->setManualStatus($status)
            ->setAutomaticStatus($status)
            ->setAmountCrypto($payload->cryptoAmount)
            ->setReceivedAmountCrypto(0)
            ->setIsAutomatic(false)
            ->setCreatedAt($now)
            ->setExpiredAt($now->modify('+30 minutes'))
            ->setTxHash(Uuid::uuid4()->toString());

        $this->em->persist($tx);

		$this->eventDispatcher->dispatch(new TransactionCreatedEvent($tx), TransactionCreatedEvent::NAME);

	    $this->em->flush();
        return $tx;
    }

    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        if (isset($data['expired_at'])) {
            $transaction->setExpiredAt(new \DateTimeImmutable($data['expired_at']));
        }

        if (isset($data['main_status_id'])) {
            $mainStatus = $this->em->getRepository(PaymentStatus::class)->find($data['main_status_id']);
            if ($mainStatus) {
                $transaction->setMainStatus($mainStatus);
            }
        }

        if (isset($data['manual_status_id'])) {
            $manualStatus = $this->em->getRepository(PaymentStatus::class)->find($data['manual_status_id']);
            if ($manualStatus) {
                $transaction->setManualStatus($manualStatus);
            }
        }

        if (isset($data['automatic_status_id'])) {
            $automaticStatus = $this->em->getRepository(PaymentStatus::class)->find($data['automatic_status_id']);
            if ($automaticStatus) {
                $transaction->setAutomaticStatus($automaticStatus);
            }
        }

        if (isset($data['confirmation_id'])) {
            $confirmation = $this->em->getRepository(PaymentConfirmation::class)->find($data['confirmation_id']);
            $transaction->setConfirmation($confirmation);
        }

        if (array_key_exists('is_automatic', $data)) {
            $transaction->setIsAutomatic((bool)$data['is_automatic']);
        }

        $this->em->flush();

        return $transaction;
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        if ($transaction->getMainStatus()->getCode() === 'CMP') {
            throw new \RuntimeException('Cannot delete completed transaction.');
        }

        foreach ($transaction->getFiatAmounts() as $fiatAmount) {
            $this->em->remove($fiatAmount);
        }

        $this->em->remove($transaction);
        $this->em->flush();
    }
}