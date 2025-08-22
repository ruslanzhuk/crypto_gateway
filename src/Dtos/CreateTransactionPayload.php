<?php

namespace App\Dtos;

use App\Entity\CryptoCurrency;
use App\Entity\FiatCurrency;
use App\Entity\Network;
use App\Entity\User;

class CreateTransactionPayload
{
    public function __construct(
        public readonly User $user,
        public readonly CryptoCurrency $cryptoCurrency,
        public readonly Network $network,
        public readonly float $cryptoAmount,
    ) {}
}