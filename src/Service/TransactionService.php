<?php

namespace App\Service;

use App\Dtos\CreateTransactionPayload;
use App\Entity\FiatAmountPerTransaction;
use App\Entity\PaymentConfirmation;
use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Entity\PaymentStatus;
use App\Service\Provider\Price\CoinGeckoPriceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Ramsey\Uuid\Uuid;

class TransactionService
{
    private EntityManagerInterface $em;
    private ParameterBagInterface $params;
    private CoinGeckoPriceProvider $priceProvider;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, CoinGeckoPriceProvider $priceProvider)
    {
        $this->em = $em;
        $this->params = $params;
        $this->priceProvider = $priceProvider;
    }

    public function getWalletAddress(string $cryptoCode, string $networkCode): string
    {
        $wallets = $this->params->get('wallets');

        return $wallets[$networkCode][$cryptoCode] ?? '0xDEFAULTADDRESS';
    }

    public function createTransaction(CreateTransactionPayload $payload): Transaction
    {
        $now = new \DateTimeImmutable();

        $fiatRates = $this->priceProvider->convertCryptoToFiat(
            strtolower($payload->cryptoCurrency->getName()),
            $payload->cryptoAmount
        );

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
            ->setCryptoCurrency($payload->cryptoCurrency)
            ->setMainStatus($status)
            ->setManualStatus($status)
            ->setAutomaticStatus($status)
//            ->setConfirmation($confirmation)
            ->setAmountCrypto($payload->cryptoAmount)
            ->setReceivedAmountCrypto(0)
            ->setIsAutomatic(false)
            ->setCreatedAt($now)
            ->setExpiredAt($now->modify('+30 minutes'))
            ->setTxHash(Uuid::uuid4()->toString()); // або uuid або більш надійний хеш

        $this->em->persist($tx);

        foreach ($fiatRates as $fiatCode => $fiatAmount) {
            $fiatCurrency = $this->em->getRepository(\App\Entity\FiatCurrency::class)
                ->findOneBy(['code' => strtoupper($fiatCode)]);

            if (!$fiatCurrency) {
                throw new \RuntimeException("Currency $fiatCode not found in fiat_currency table.");
            }

            // Ціна за 1 одиницю крипти (тобто rate)
            $rate = $fiatAmount / $payload->cryptoAmount;

            $conversion = (new FiatAmountPerTransaction())
                ->setTransaction($tx)
                ->setFiatCurrency($fiatCurrency)
                ->setAmount($fiatAmount)
                ->setRate($rate)
                ->setCreatedAt($now);

            $this->em->persist($conversion);
        }

        $this->em->flush();

        return $tx;
    }
}