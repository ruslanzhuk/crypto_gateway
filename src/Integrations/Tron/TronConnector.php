<?php

namespace App\Integrations\Tron;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TronConnector
{
    private const BASE_URL = 'https://api.trongrid.io';

    public function __construct(private readonly HttpClientInterface $httpClient) {}

    public function get(string $endpoint, array $query = []): ResponseInterface {
        return $this->httpClient->request('GET', self::BASE_URL . $endpoint, [
            'query' => array_merge($query, ['apiKey' => $_ENV['TRONGRID_API_KEY']]),
        ]);
    }
}
