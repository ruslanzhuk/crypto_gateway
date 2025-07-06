<?php

namespace App\Controller;

use App\Repository\CryptoCurrencyRepository;
use App\Repository\FiatCurrencyRepository;
use App\Repository\NetworkRepository;
use App\Repository\UserRepository;
use App\Service\TransactionProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/payment', name: 'api_payment', methods: ['POST'])]
final class PaymentController extends AbstractController
{
    #[Route('/', name: 'create')]
    public function create(Request $request, UserRepository $userRepo,
                           FiatCurrencyRepository $fiatRepo, CryptoCurrencyRepository $cryptoRepo,
                           NetworkRepository $networkRepo, TransactionProcessor $processor,
                           EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $shop = $userRepo->findOneBy(['name' => $data['name']]);
        if (!$shop) {
            return new JsonResponse(['error' => 'Shop not found'], 404);
        }

        $fiat = $fiatRepo->findOneBy(['code' => strtoupper($data['fiat'])]);
        $crypto = $cryptoRepo->findOneBy(['code' => strtoupper($data['crypto'])]);
        $network = $networkRepo->findOneBy(['code' => strtolower($data['network'])]);

        if (!$fiat || !$crypto || !$network) {
            return new JsonResponse(['error' => 'Currency or network not found'], 400);
        }

        $walletAddress = $processor->getWalletAddress($crypto->getCode(), $network->getCode());

        $transaction = $processor->createTransaction($shop, $fiat, $crypto, $network, $data['amount'], $walletAddress);

        $em->persist($transaction);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'wallet_address' => $walletAddress,
            'tx_id' => $transaction->getId(),
        ]);
    }
}
