<?php

namespace App\Controller\Api;

use App\Dtos\CreatePaymentRequestDTO;
use App\Dtos\CreateTransactionPayload;
use App\Entity\Transaction;
use App\Factory\TransactionPayloadFactory;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use App\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/transaction')]
final class TransactionController extends AbstractController
{
    public function __construct(private UserRepository $userRepo)
    {
    }

    #[Route(name: 'app_transaction_index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository): Response
    {
        return $this->render('api/transaction/index.html.twig', [
            'transactions' => $transactionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, TransactionService $transactionService, TransactionPayloadFactory $payloadFactory): Response
    {
        /* @var CreatePaymentRequestDTO $data */

        $data = $request->attributes->get("payload");

        $payload = $payloadFactory->fromDto($data);

        $transaction = $transactionService->createTransaction($payload);


        // Зберегти всі дані
        $em->flush();

        // Відповідь
        return $this->json([
            'success' => true,
            'transaction_id' => $transaction->getId(),
            'tx_hash' => $transaction->getTxHash(),
            'wallet' => $transaction->getWallet()->getPublicAddress(),
            'expires_at' => $transaction->getExpiredAt()->format('Y-m-d H:i:s'),
        ]);

//        return $this->render('api/transaction/new.html.twig', [
//            'transaction' => $transaction,
//            'form' => $form,
//        ]);
    }

    #[Route('/show/{id}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        return $this->render('api/transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api/transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
