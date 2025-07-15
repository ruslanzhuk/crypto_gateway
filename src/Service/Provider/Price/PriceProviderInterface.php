<?php

namespace App\Service\Provider\Price;

interface PriceProviderInterface
{
    public function supports(string $network): bool;

    public function convertFiatToCrypto(string $fiatCurrency, string $cryptoCurrency, float $fiatAmount): float;

}