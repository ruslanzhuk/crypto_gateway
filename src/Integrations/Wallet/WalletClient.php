<?php

namespace App\Integrations\Wallet;


class WalletClient
{
    public function __construct(private readonly WalletConnector $connector)
    {
    }

    public function generateWallet(string $network) {
        $response = $this->connector->post("/api/generate", [
            "network" => $network
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Wallet microservice error: Failed to create new wallet.' . $response->getContent(false));
        }

        return $response->toArray();
    }

    public function calculatePK($data) {
        $response = $this->connector->get("/api/calculate/public_key", [
            "network" => $data["network"],
            "private_key" => $data["private_key"],
        ]);

        return $response->toArray();
    }

}