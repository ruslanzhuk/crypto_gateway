<?php

namespace App\Service\Externalapi\Tron;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TronConnector
{
    private const BASE_URL = 'https://api.tronscan.org';

    public function __construct(private readonly HttpClientInterface $httpClient) {}

    public function get(string $endpoint, array $query = []): ResponseInterface
    {
        return $this->httpClient->request('GET', self::BASE_URL . $endpoint, [
            'query' => $query,
        ]);
    }
}
