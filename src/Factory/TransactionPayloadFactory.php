<?php

namespace App\Factory;

use App\Dtos\CreatePaymentRequestDTO;
use App\Dtos\CreateTransactionPayload;
use App\Repository\CryptoCurrencyRepository;
use App\Repository\FiatCurrencyRepository;
use App\Repository\NetworkRepository;
use App\Repository\UserRepository;
use App\Service\TransactionService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransactionPayloadFactory
{
    public function __construct(
        private UserRepository $userRepository,
        private FiatCurrencyRepository $fiatRepo,
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

        $fiat = $this->fiatRepo->findOneBy(['code' => $dto->fiatcurrency]);
        if (!$fiat) {
            throw new NotFoundHttpException('Fiat currency not found');
        }

        $crypto = $this->cryptoRepo->findOneBy(['code' => $dto->cryptocurrency]);
        if (!$crypto) {
            throw new NotFoundHttpException('Cryptocurrency not found');
        }

        $network = $this->networkRepo->findOneBy(['name' => $dto->network]);
        if (!$network) {
            throw new NotFoundHttpException('Network not found');
        }

        $walletAddress = $this->txService->getWalletAddress($dto->cryptocurrency, $dto->network);



        return new CreateTransactionPayload(
            user: $user,
            fiatCurrency: $fiat,
            cryptoCurrency: $crypto,
            network: $network,
            fiatAmount: $dto->fiatamount,
            walletAddress: $walletAddress
        );
    }
}
