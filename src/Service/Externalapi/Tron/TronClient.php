<?php

namespace App\Service\Externalapi\Tron;

class TronClient
{
    public function __construct(private TronConnector $connector) {}

    public function fetchCryptoPrice(string $fiatCurrency, string $cryptoCurrency, float $fiatAmount): float
    {
        $response = $this->connector->get('/price', [
            'crypto' => $cryptoCurrency,
            'fiat' => $fiatCurrency,
            'amount' => $fiatAmount,
        ]);

        $data = $response->toArray();

        if (!isset($data['cryptoAmount'])) {
            throw new \RuntimeException('Unexpected response from Tron API');
        }

        return (float) $data['cryptoAmount'];
    }
}
