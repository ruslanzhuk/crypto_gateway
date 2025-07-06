<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'wallets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'wallets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Network $network = null;

    #[ORM\Column(length: 255)]
    private ?string $public_address = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $private_key = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $seed_phrase = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(?Network $network): static
    {
        $this->network = $network;

        return $this;
    }

    public function getPublicAddress(): ?string
    {
        return $this->public_adress;
    }

    public function setPublicAddress(string $public_adress): static
    {
        $this->public_adress = $public_adress;

        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->private_key;
    }

    public function setPrivateKey(string $private_key): static
    {
        $this->private_key = $private_key;

        return $this;
    }

    public function getSeedPhrase(): ?string
    {
        return $this->seed_phrase;
    }

    public function setSeedPhrase(string $seed_phrase): static
    {
        $this->seed_phrase = $seed_phrase;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
