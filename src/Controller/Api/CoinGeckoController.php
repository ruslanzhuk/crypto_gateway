<?php

namespace App\Controller\Api;

use App\Service\Externalapi\CoinGecko\CoinGeckoClient;
use App\Service\Provider\Price\CoinGeckoPriceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CoinGeckoController extends AbstractController
{
    #[Route('/api/crypto/price', name: 'api_crypto_price')]
    public function getPrice(CoinGeckoClient $client): JsonResponse
    {
        $data = $client->getSimplePrice('bitcoin', 'usd');

        return $this->json($data);
    }

    #[Route('/api/crypto/convert/from-crypto', name: 'api_crypto_convert_from_crypto', methods: ['GET'])]
    public function convertFromCrypto(Request $request, CoinGeckoPriceProvider $priceProvider): JsonResponse
    {
        $crypto = $request->query->get('crypto', 'usdt');
        $amount = (float) $request->query->get('amount', 0);

        if ($amount <= 0) {
            return $this->json([
                'error' => 'Invalid amount.',
            ], 400);
        }

        try {
            $converted = $priceProvider->convertCryptoToFiat($crypto, $amount);

            return $this->json([
                'crypto' => strtoupper($crypto),
                'amount' => $amount,
                'converted' => $converted,
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Conversion failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/api/crypto/price/tron', name: 'api_crypto_price')]
    public function getPriceTron(CoinGeckoClient $client): JsonResponse
    {
        $crypto = 'tron'; // TRX
        $fiat = 'usd';
        $fiatAmount = 100.0;

        $priceData = $client->getSimplePrice($crypto, $fiat);

        if (!isset($priceData[$crypto][$fiat])) {
            return $this->json([
                'error' => 'Price not found for this crypto/fiat pair.',
            ], 400);
        }

        $price = $priceData[$crypto][$fiat]; // Ціна 1 TRX в USD
        $cryptoAmount = $fiatAmount / $price;

        return $this->json([
            'crypto' => strtoupper($crypto),
            'fiat' => strtoupper($fiat),
            'price_per_unit' => $price,
            'fiat_amount' => $fiatAmount,
            'converted_crypto_amount' => $cryptoAmount,
        ]);
    }

    #[Route('/api/crypto/list', name: 'api_crypto_list')]
    public function getCoinList(CoinGeckoClient $client): JsonResponse
    {
        $data = $client->getSupportedCoins();

        return $this->json($data);
    }
}
