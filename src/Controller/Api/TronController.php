<?php

namespace App\Controller\Api;

use App\Service\Externalapi\Tron\TronClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TronController extends AbstractController
{
    #[Route('/api/tron/price', name: 'api_tron_price')]
    public function getPrice(TronClient $client): JsonResponse
    {
        // Просто дефолтні тестові значення для прикладу:
        $fiatCurrency = 'USD';
        $cryptoCurrency = 'USDT';
        $fiatAmount = 100;

        try {
            $price = $client->fetchCryptoPrice($fiatCurrency, $cryptoCurrency, $fiatAmount);

            return $this->json([
                'fiat' => $fiatAmount,
                'crypto' => $price,
                'fiat_currency' => $fiatCurrency,
                'crypto_currency' => $cryptoCurrency,
                'source' => 'tron'
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'details' => $e instanceof \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
                    ? 'TransportException'
                    : get_class($e)
            ], 500);
        }
    }
}
