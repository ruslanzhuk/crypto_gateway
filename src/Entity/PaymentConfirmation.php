<?php

namespace App\Entity;

use App\Repository\PaymentConfirmationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentConfirmationRepository::class)]
class PaymentConfirmation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $confirmedBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $confirmedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfirmedBy(): ?User
    {
        return $this->confirmedBy;
    }

    public function setConfirmedBy(?User $confirmed_by): static
    {
        $this->confirmedBy = $confirmed_by;

        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(\DateTimeImmutable $confirmed_at): static
    {
        $this->confirmedAt = $confirmed_at;

        return $this;
    }
}
