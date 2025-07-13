<?php

namespace App\Service;

use App\Dtos\CreateTransactionPayload;
use App\Entity\PaymentConfirmation;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\FiatCurrency;
use App\Entity\CryptoCurrency;
use App\Entity\Network;
use App\Entity\Wallet;
use App\Entity\PaymentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Ramsey\Uuid\Uuid;

class TransactionService
{
    private EntityManagerInterface $em;
    private ParameterBagInterface $params;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->params = $params;
    }

    public function getWalletAddress(string $cryptoCode, string $networkCode): string
    {
        $wallets = $this->params->get('wallets');

        return $wallets[$networkCode][$cryptoCode] ?? '0xDEFAULTADDRESS';
    }

    public function createTransaction(CreateTransactionPayload $payload): Transaction
    {
        $now = new \DateTimeImmutable();

        $wallet = (new Wallet())
            ->setPublicAddress($payload->walletAddress)
            ->setPrivateKey('STATIC-PRIVATE-KEY') // ⚠️ замінити на реальне генерування
            ->setSeedPhrase('STATIC-SEED')        // ⚠️ замінити на реальне генерування
            ->setCreatedAt($now)
            ->setUser($payload->user)
            ->setNetwork($payload->network);

        $this->em->persist($wallet);

        $status = $this->em->getRepository(PaymentStatus::class)->findOneBy(['code' => 'PND']);
        if (!$status) {
            throw new \RuntimeException("Status 'PND' not be found in table payment_status.");
        }

//        $confirmation = new PaymentConfirmation();
//        $this->em->persist($confirmation); // ⚠️ якщо confirmation обов’язковий і створюється одразу

        $tx = (new Transaction())
            ->setUser($payload->user)
            ->setWallet($wallet)
            ->setFiatCurrency($payload->fiatCurrency)
            ->setCryptoCurrency($payload->cryptoCurrency)
            ->setMainStatus($status)
            ->setManualStatus($status)
            ->setAutomaticStatus($status)
//            ->setConfirmation($confirmation)
            ->setAmountFiat($payload->fiatAmount)
            ->setAmountCrypto(0)
            ->setReceivedAmountFiat(0)
            ->setReceivedAmountCrypto(0)
            ->setIsAutomatic(false)
            ->setCreatedAt($now)
            ->setExpiredAt($now->modify('+30 minutes'))
            ->setTxHash(Uuid::uuid4()->toString()); // або uuid або більш надійний хеш

        $this->em->persist($tx);

        $this->em->flush();

        return $tx;
    }
}