<?php

namespace App\Transformer;

use App\Dtos\CreatePaymentRequestDTO;
use App\Dtos\CreateTransactionPayload;
use App\Repository\CryptoCurrencyRepository;
use App\Repository\FiatCurrencyRepository;
use App\Repository\NetworkRepository;
use App\Repository\UserRepository;
use App\Service\TransactionService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransactionPayloadTransformer
{
    public function __construct(
        private UserRepository $userRepository,
        private CryptoCurrencyRepository $cryptoRepo,
        private NetworkRepository $networkRepo,
        private TransactionService $txService,
    )
    {
    }

    public function fromDto(CreatePaymentRequestDTO $dto): CreateTransactionPayload
    {
        $user = $this->userRepository->findOneBy(['name' => $dto->shop]);
        if (!$user) {
            throw new NotFoundHttpException('Shop not found');
        }

        $crypto = $this->cryptoRepo->findOneBy(['code' => $dto->cryptocurrency]);
        if (!$crypto) {
            throw new NotFoundHttpException('Cryptocurrency not found');
        }

        $network = $this->networkRepo->findOneBy(['id' => $crypto->getNetwork()]);
        if (!$network) {
            throw new NotFoundHttpException('Network not found');
        }

        return new CreateTransactionPayload(
            user: $user,
            cryptoCurrency: $crypto,
            network: $network,
            cryptoAmount: $dto->cryptoamount,
        );
    }
}
