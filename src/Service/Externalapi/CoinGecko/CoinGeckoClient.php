<?php

namespace App\Service\Externalapi\CoinGecko;

class CoinGeckoClient
{
    public function __construct(private readonly CoinGeckoConnector $connector) {}

    public function getSimplePrice(string $crypto, string $fiat): array
    {
        $response = $this->connector->get('/simple/price', [
            'ids' => $crypto,
            'vs_currencies' => $fiat,
        ]);

        return $response->toArray();
    }

    public function getSupportedCoins(): array
    {
        $response = $this->connector->get('/coins/list');

        return $response->toArray();
    }
}