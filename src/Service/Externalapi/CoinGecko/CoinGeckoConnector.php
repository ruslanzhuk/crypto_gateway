<?php

namespace App\Service\Externalapi\CoinGecko;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CoinGeckoConnector
{
    private const BASE_URL = 'https://api.coingecko.com/api/v3';

    public function __construct(private readonly HttpClientInterface $httpClient) {}

    public function get(string $endpoint, array $query = []): ResponseInterface
    {
        return $this->httpClient->request('GET', self::BASE_URL . $endpoint, [
            'query' => $query,
        ]);
    }
}