<?php

namespace App\Service\Provider\Price;

use App\Integrations\Tron\TronClient;

class TronPriceProvider implements PriceProviderInterface
{
    public function __construct(
        private readonly TronClient $tronClient
    ) {}

    public function supports(string $network): bool
    {
        return strtolower($network) === 'tron';
    }

    public function convertFiatToCrypto(string $fiatCurrency, string $cryptoCurrency, float $fiatAmount): float
    {
        return $this->tronClient->fetchCryptoPrice($fiatCurrency, $cryptoCurrency, $fiatAmount);
    }
}
