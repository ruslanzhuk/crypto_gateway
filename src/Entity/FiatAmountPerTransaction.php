<?php

namespace App\Entity;

use App\Repository\FiatAmountPerTransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FiatAmountPerTransactionRepository::class)]
#[UniqueEntity(fields: ['transaction', 'fiatCurrency'], message: 'This fiat currency already exists for this transaction.')]
#[ORM\Table(name: 'fiat_amount_per_transaction')]
#[ORM\UniqueConstraint(name: 'uniq_transaction_currency', columns: ['transaction_id', 'fiat_currency_id'])]
class FiatAmountPerTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'fiatAmountPerTransactions')]
    private ?Transaction $transaction = null;

    #[ORM\ManyToOne(inversedBy: 'fiatAmountPerTransactions')]
    private ?FiatCurrency $fiatCurrency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 36, scale: 18)]
    private ?string $rate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): static
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getFiatCurrency(): ?FiatCurrency
    {
        return $this->fiatCurrency;
    }

    public function setFiatCurrency(?FiatCurrency $fiatCurrency): static
    {
        $this->fiatCurrency = $fiatCurrency;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
