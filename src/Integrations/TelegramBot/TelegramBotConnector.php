<?php

namespace App\Integrations\TelegramBot;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TelegramBotConnector
{
	private const BASE_URL = 'https://api.telegram.org/bot';

	public function __construct(
		private readonly HttpClientInterface $httpClient,
	){}

	public function get(string $botToken, string $endpoint, array $query = []): ResponseInterface {
        return $this->httpClient->request('GET', self::BASE_URL . "$botToken/$endpoint", [
			'query' => $query,
        ]);
    }

	public function post(string $botToken, string $endpoint, array $data = []): ResponseInterface {
		return $this->httpClient->request('POST', self::BASE_URL . "$botToken/$endpoint", [
			'json' => $data,
		]);
	}
}