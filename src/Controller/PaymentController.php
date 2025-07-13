<?php

namespace App\Controller;

use App\Dtos\CreatePaymentRequestDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/payment', name: 'api_payment')]
final class PaymentController extends AbstractController
{
    public function __construct() {}

    #[Route('/', name: 'gateway', methods: ['POST'])]
    public function create(CreatePaymentRequestDTO $request): Response
    {
        return $this->forward(
            controller: 'App\\Controller\\Api\\Admin\\TransactionController::new',
            path: [
                'payload' => $request,
            ]
        );
    }
}
