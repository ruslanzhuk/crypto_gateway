<?php

namespace App\Dtos;

use App\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePaymentRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Choice(['create_payment'])]
    public string $operation_type;

    #[Assert\NotBlank]
    public string $shop;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\Positive]
    public float $cryptoamount;

    #[Assert\NotBlank]
    #[AppAssert\CryptoCurrencyExists]
    public string $cryptocurrency;
}