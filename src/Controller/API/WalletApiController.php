<?php

namespace App\Controller\API;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use App\Service\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/wallet')]
class WalletApiController extends AbstractController
{
    public function __construct(
        private WalletRepository $walletRepository,
        private WalletService $walletService,
    ) {}

    #[Route('/', name: 'api_wallet_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $wallets = $this->walletRepository->findAll();

        return $this->json($wallets);
    }

    #[Route('/show/{id}', name: 'api_wallet_show', methods: ['GET'])]
    public function show(Wallet $wallet): JsonResponse
    {
        return $this->json($wallet);
    }

    #[Route('/edit/{id}', name: 'api_wallet_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Wallet $wallet): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->walletService->updateWallet($wallet, $data);

        return $this->json([
            'success' => true,
            'message' => 'Wallet updated successfully',
            'wallet' => $wallet,
        ]);
    }

    #[Route('/delete/{id}', name: 'api_wallet_delete', methods: ['DELETE'])]
    public function delete(Wallet $wallet, Request $request): JsonResponse
    {
        $force = $request->query->getBoolean('force', false);

        try {
            $this->walletService->deleteWallet($wallet, $force);

            return $this->json([
                'success' => true,
                'message' => $force
                    ? 'Wallet and its linked transaction were deleted successfully.'
                    : 'Wallet deleted successfully.',
            ]);
        } catch (\RuntimeException $e) {
            $linkedTx = $this->walletService->getLinkedTransaction($wallet);

            return $this->json([
                'error' => true,
                'message' => $e->getMessage(),
                'linked_transaction' => $linkedTx ? [
                    'id' => $linkedTx->getId(),
                    'txHash' => $linkedTx->getTxHash(),
                ] : null,
            ], 409);
        }
    }
}