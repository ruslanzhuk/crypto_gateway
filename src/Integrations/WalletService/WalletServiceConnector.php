<?php

namespace App\Integrations\WalletService;

use Wallet\GenerateWalletRequest;
use Wallet\WalletServiceClient as GenerateWalletClient;
use Grpc\ChannelCredentials;
use Psr\Log\LoggerInterface;

class WalletServiceConnector
{
    private GenerateWalletClient $client;

    public function __construct(string $host, bool $useSsl = true, private LoggerInterface $logger)
    {
        $creditials = $useSsl
            ? ChannelCredentials::createSsl()
            : ChannelCredentials::createInsecure();

        $host = str_replace(['https://', 'http://'], '', $host);

        $this->client = new GenerateWalletClient($host, [
            'credentials' => $creditials,
        ]);

        $this->logger->info("Initialized gRPC client for WalletService", [
            'host' => $host,
            'ssl' => $useSsl,
        ]);
    }

    public function generateWallet(string $network): array
    {
        $this->logger->info("Sending gRPC request to generate wallet", [
            'network' => $network,
        ]);

        $request = new GenerateWalletRequest();
        $request->setNetwork($network);

        list($response, $status) = $this->client->GenerateWallet($request)->wait();

        if ($status->code !== \Grpc\STATUS_OK) {
            $this->logger->error("gRPC error", [
                'code' => $status->getCode(),
                'details' => $status->getDetails(),
            ]);
            throw new \RuntimeException("gRPC error: {$status->details}");
        }

        $this->logger->info("Successfully received wallet", [
            'public_address' => $response->getPublicAddress(),
        ]);

        return [
            'public_address' => $response->getPublicAddress(),
            'private_key' => $response->getPrivateKey(),
            'network' => $response->getNetwork(),
            'mnemonic' => $response->getMnemonic(),
        ];
    }


}