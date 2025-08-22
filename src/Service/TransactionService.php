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
use App\Service\Provider\Price\CoinGeckoPriceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;

class TransactionService
{
    private EntityManagerInterface $em;
    private ParameterBagInterface $params;
    private CoinGeckoPriceProvider $priceProvider;
	private FiatConversionService $fiatConversionService;

	private TelegramBotService $telegramBotService;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, CoinGeckoPriceProvider $priceProvider, FiatConversionService $fiatConversionService, TelegramBotService $telegramBotService)
    {
        $this->em = $em;
        $this->params = $params;
        $this->priceProvider = $priceProvider;
		$this->fiatConversionService = $fiatConversionService;
		$this->telegramBotService = $telegramBotService;
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

        $this->fiatConversionService->createConversions(
			$tx,
	        $payload->cryptoAmount,
	        $payload->cryptoCurrency->getName(),
        );

        $this->em->flush();

		$telegramIntegration = $this->em->getRepository(TelegramBotIntegration::class)->findOneBy(['creator' => $payload->user]);

		$loggerChats = $this->em->getRepository(TelegramBotChat::class)->findBy(['integration' => $telegramIntegration,'role' => 'ROLE_LOGGER_CHAT', 'isVerified' => true]);
		foreach ($loggerChats as $chat) {
			$chatId = $chat->getChatId();
			$this->telegramBotService->notifyUser($telegramIntegration, $chatId,
				"🆕 Нова транзакція створена!\nСума: {$tx->getAmountCrypto()} {$tx->getCryptoCurrency()->getName()}\nКористувач: {$tx->getUser()->getEmail()}\nTxHash: {$tx->getTxHash()}");
		}

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