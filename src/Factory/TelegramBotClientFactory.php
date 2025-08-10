<?php

namespace App\Factory;

use App\Integrations\TelegramBot\TelegramBotClient;
use App\Integrations\TelegramBot\TelegramBotConnector;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramBotClientFactory
{
	public function __construct(private readonly HttpClientInterface $httpClient, private readonly LoggerInterface $logger)
	{}

	public function create(string $token): TelegramBotClient
	{
		$connector = new TelegramBotConnector($this->httpClient);
		return new TelegramBotClient($connector, $token, $this->logger);
	}
}