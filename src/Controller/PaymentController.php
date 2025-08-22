<?php

namespace App\Controller;

use App\Dtos\CreatePaymentRequestDTO;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payment')]
final class PaymentController extends AbstractController
{
    public function __construct() {}

    #[Route('/', name: 'payment_gateway', methods: ['POST'])]
    public function create(CreatePaymentRequestDTO $request): Response
    {
        $response = $this->forward(
            controller: 'App\\Controller\\API\\TransactionApiController::new',
            path: [
                'payload' => $request,
            ]
        );

		$data = json_decode($response->getContent(), true);

		return $this->redirectToRoute('payment_show', [
			'tx_hash' => $data['tx_hash'],
		]);
    }

	#[Route('/page/{tx_hash}', name: 'payment_show', methods: ['GET'])]
	public function show(string $tx_hash, EntityManagerInterface $em): Response
	{
		$transaction = $em->getRepository(Transaction::class)
			->findOneBy(['txHash' => $tx_hash]);

		if (!$transaction) {
			throw $this->createNotFoundException('Transaction not found.');
		}

		return $this->render('payment/show.html.twig', [
			'transaction' => $transaction,
		]);
	}
}
