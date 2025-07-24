<?php

namespace App\Integrations\Wallet;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


class WalletConnector
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $walletServiceApiURL,
    )
    {
    }

    public function get(string $endpoint, array $query = []): ResponseInterface
    {
        return $this->httpClient->request('GET', $this->walletServiceApiURL . $endpoint, [
            'query' => $query,
        ]);
    }

    public function post(string $endpoint, array $data = []): ResponseInterface
    {
        return $this->httpClient->request('POST', $this->walletServiceApiURL . $endpoint, [
            'json' => $data,
        ]);
    }
}