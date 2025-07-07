<?php

namespace App\Dtos;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePaymentRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Choice(['create_payment'])]
    public string $operation_type;

    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\Positive]
    public float $amount;

    #[Assert\NotBlank]
    #[Assert\Currency]
    public string $fiatcurrency;

    #[Assert\NotBlank]
    public string $cryptocurrency;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 10)]
    public string $network;
}