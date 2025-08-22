<?php

namespace App\Service;

use App\Entity\FiatAmountPerTransaction;
use App\Entity\FiatCurrency;
use App\Entity\Transaction;
use App\Service\Provider\Price\CoinGeckoPriceProvider;
use Doctrine\ORM\EntityManagerInterface;

class FiatConversionService
{
	public function __construct(
		private EntityManagerInterface $em,
		private CoinGeckoPriceProvider $priceProvider,
	) {}

	public function createConversions(Transaction $transaction, float $cryptoAmount, string $cryptoCurrency): array
	{
		$now = new \DateTimeImmutable();

		$fiatRates = $this->priceProvider->convertCryptoToFiat($cryptoCurrency, $cryptoAmount);

		$conversions = [];
		foreach ($fiatRates as $fiatCode => $fiatAmount) {
			$fiatCurrency = $this->em->getRepository(FiatCurrency::class)->findOneBy(['code' => strtoupper($fiatCode)]);

			if (!$fiatCurrency) {
				throw new \RuntimeException("Currency $fiatCode not found");
			}

			$rate = $fiatAmount / $cryptoAmount;
			$conversion = (new FiatAmountPerTransaction())
				->setTransaction($transaction)
				->setFiatCurrency($fiatCurrency)
				->setAmount($fiatAmount)
				->setRate($rate)
				->setCreatedAt($now);

			$this->em->persist($conversion);
			$conversions[] = $conversion;
		}

		return $conversions;
	}

}