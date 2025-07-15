<?php

namespace App\Service\Provider\Price;

use App\Service\Externalapi\CoinGecko\CoinGeckoClient;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class CoinGeckoPriceProvider
{
    public function __construct(private readonly CoinGeckoClient $client) {}

    public function convertFiatToCrypto(string $fiat, string $crypto, float $fiatAmount): float
    {
        $fiat = strtolower($fiat);
        $crypto = strtolower($crypto);

        $priceData = $this->client->getSimplePrice($crypto, $fiat);

        if (!isset($priceData[$crypto][$fiat]) || $priceData[$crypto][$fiat] <= 0) {
            throw new \RuntimeException("Cannot fetch price for $crypto/$fiat");
        }

        $price = BigDecimal::of((string)$priceData[$crypto][$fiat]);
        $amount = BigDecimal::of((string)$fiatAmount)->dividedBy($price, 18, RoundingMode::DOWN);

        return rtrim(rtrim($amount->__toString(), '0'), '.');
    }

    public function convertCryptoToFiat(string $crypto, float $cryptoAmount, array $fiatCurrencies = ['usd', 'eur', 'gbp']): array
    {
        $crypto = strtolower($crypto);
        $fiats = array_map('strtolower', $fiatCurrencies);

        $priceData = $this->client->getSimplePrice($crypto, implode(',', $fiats));

        if (!isset($priceData[$crypto])) {
            throw new \RuntimeException("Cannot fetch prices for $crypto");
        }

        $result = [];
        foreach ($fiats as $fiat) {
            if (!isset($priceData[$crypto][$fiat])) {
                throw new \RuntimeException("Price for $crypto in $fiat not found");
            }

            $price = BigDecimal::of((string)$priceData[$crypto][$fiat]);
            $amount = BigDecimal::of((string)$cryptoAmount)->multipliedBy($price)->toScale(2, RoundingMode::DOWN);
            $result[$fiat] = (float) $amount->__toString();
        }

        return $result;
    }
}