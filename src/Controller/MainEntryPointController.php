<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainEntryPointController extends AbstractController
{
    #[Route('/entrypoint', name: 'entry_point', methods: ['POST'])]
    public function handleEntry(Request $request): Response
    {
        $json = json_decode($request->getContent(), true);

        // 1. Validate request
        // 2. Determine the type of operation
        $operation = $json['operation_type'] ?? null;

        // 3. Redirect to another service
        switch ($operation) {
            case 'create_payment':
                return $this->forward('App\Controller\PaymentController::create', [
                    'payload' => $json,
                ]);
            case 'status_check':
                return $this->forward('App\Controller\StatusController::checkStatus', [
                    'payload' => $json,
                ]);
            default:
                return $this->json(['error' => 'Unknown operation'], 400);
        }
    }
}
