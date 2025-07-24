<?php

namespace App\Controller\API;

use App\Dtos\CreatePaymentRequestDTO;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Service\TransactionService;
use App\Service\WalletService;
use App\Transformer\TransactionPayloadTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/transaction')]
class TransactionApiController extends AbstractController
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private TransactionService $transactionService,
        private WalletService $walletService,
        private TransactionPayloadTransformer $payloadTransformer,
        private EntityManagerInterface $em,
        private NormalizerInterface $normalizer
    )
    {
    }

    #[Route('/', name: 'api_transaction_index', methods: ['GET'])]
    public function get_all(): JsonResponse {
        $transactions = $this->transactionRepository->findAll();
        return $this->json($transactions);
    }

    #[Route('/new', name: 'api_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, TransactionService $transactionService, TransactionPayloadTransformer $payloadTransformer, WalletService $walletService): Response
    {
        /* @var CreatePaymentRequestDTO $data */
        $data = $request->attributes->get("payload");

        $payload = $payloadTransformer->fromDto($data);

        $transaction = $transactionService->createTransaction($payload, $walletService);


        return $this->json([
            'success' => true,
            'transaction_id' => $transaction->getId(),
            'tx_hash' => $transaction->getTxHash(),
            'wallet' => $transaction->getWallet()->getPublicAddress(),
            'expires_at' => $transaction->getExpiredAt()->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/show/{id}', name: 'api_transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): JsonResponse
    {
        return $this->json($transaction);
    }

    #[Route('/edit/{id}', name: 'api_transaction_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Transaction $transaction): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->transactionService->updateTransaction($transaction, $data);

        return $this->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction,
        ]);
    }

    #[Route('/delete/{id}', name: 'api_transaction_delete', methods: ['DELETE'])]
    public function delete(Request $request, Transaction $transaction): JsonResponse
    {
        $this->transactionService->deleteTransaction($transaction);

        return $this->json([
            'success' => true,
            'message' => 'Transaction deleted successfully',
        ]);
    }
}