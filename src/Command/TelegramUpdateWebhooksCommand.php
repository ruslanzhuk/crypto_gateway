<?php

namespace App\Command;

use App\Repository\TelegramBotIntegrationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'telegram:update-webhooks')]
class TelegramUpdateWebhooksCommand extends Command
{
	public function __construct(
		private readonly TelegramBotIntegrationRepository $botRepo,
		private readonly HttpClientInterface $http,
	)
	{
		parent::__construct();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$baseUrl = "https://293ff34d886f.ngrok-free.app/telegram/webhook";

		$bots = $this->botRepo->findAll();

		foreach ($bots as $bot) {
			$token = $bot->getBotToken();

			$fullUrl = $baseUrl . "?token=" . $token;

			$this->http->request('GET', "https://api.telegram.org/bot{$token}/setWebhook", [
				"query" => ['url' => $fullUrl]
			]);

			$output->writeln("✅ Webhook updated for bot {$bot->getBotName()} ({$token})");
		}

		$output->writeln("🎯 All webhooks updated!");
		return Command::SUCCESS;
	}
}