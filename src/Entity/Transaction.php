<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wallet $wallet = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $txHash = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CryptoCurrency $cryptoCurrency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 36, scale: 18)]
    private ?string $amountCrypto = null;

    #[ORM\Column]
    private ?bool $isAutomatic = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentStatus $mainStatus = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentStatus $manualStatus = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?PaymentStatus $automaticStatus = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?PaymentConfirmation $confirmation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 36, scale: 18)]
    private ?string $receivedAmountCrypto = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiredAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, FiatAmountPerTransaction>
     */
    #[ORM\OneToMany(targetEntity: FiatAmountPerTransaction::class, mappedBy: 'transaction')]
    private Collection $fiatAmounts;

    public function __construct()
    {
        $this->fiatAmounts = new ArrayCollection();
    }

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

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): static
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getTxHash(): ?string
    {
        return $this->txHash;
    }

    public function setTxHash(string $txHash): static
    {
        $this->txHash = $txHash;

        return $this;
    }

    public function getCryptoCurrency(): ?CryptoCurrency
    {
        return $this->cryptoCurrency;
    }

    public function setCryptoCurrency(?CryptoCurrency $cryptoCurrency): static
    {
        $this->cryptoCurrency = $cryptoCurrency;

        return $this;
    }

    public function getAmountCrypto(): ?string
    {
        return $this->amountCrypto;
    }

    public function setAmountCrypto(string $amountCrypto): static
    {
        $this->amountCrypto = $amountCrypto;

        return $this;
    }

    public function isAutomatic(): ?bool
    {
        return $this->isAutomatic;
    }

    public function setIsAutomatic(bool $isAutomatic): static
    {
        $this->isAutomatic = $isAutomatic;

        return $this;
    }

    public function getMainStatus(): ?PaymentStatus
    {
        return $this->mainStatus;
    }

    public function setMainStatus(?PaymentStatus $mainStatus): static
    {
        $this->mainStatus = $mainStatus;

        return $this;
    }

    public function getManualStatus(): ?PaymentStatus
    {
        return $this->manualStatus;
    }

    public function setManualStatus(?PaymentStatus $manualStatus): static
    {
        $this->manualStatus = $manualStatus;

        return $this;
    }

    public function getAutomaticStatus(): ?PaymentStatus
    {
        return $this->automaticStatus;
    }

    public function setAutomaticStatus(?PaymentStatus $automaticStatus): static
    {
        $this->automaticStatus = $automaticStatus;

        return $this;
    }

    public function getConfirmation(): ?PaymentConfirmation
    {
        return $this->confirmation;
    }

    public function setConfirmation(?PaymentConfirmation $confirmation): static
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    public function getReceivedAmountCrypto(): ?string
    {
        return $this->receivedAmountCrypto;
    }

    public function setReceivedAmountCrypto(string $receivedAmountCrypto): static
    {
        $this->receivedAmountCrypto = $receivedAmountCrypto;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(\DateTimeImmutable $expiredAt): static
    {
        $this->expiredAt = $expiredAt;

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

    /**
     * @return Collection<int, FiatAmountPerTransaction>
     */
    public function getFiatAmounts(): Collection
    {
        return $this->fiatAmounts;
    }

    public function addFiatAmountPerTransaction(FiatAmountPerTransaction $fiatAmountPerTransaction): static
    {
        if (!$this->fiatAmounts->contains($fiatAmountPerTransaction)) {
            $this->fiatAmounts->add($fiatAmountPerTransaction);
            $fiatAmountPerTransaction->setTransaction($this);
        }

        return $this;
    }

    public function removeFiatAmountPerTransaction(FiatAmountPerTransaction $fiatAmountPerTransaction): static
    {
        if ($this->fiatAmounts->removeElement($fiatAmountPerTransaction)) {
            // set the owning side to null (unless already changed)
            if ($fiatAmountPerTransaction->getTransaction() === $this) {
                $fiatAmountPerTransaction->setTransaction(null);
            }
        }

        return $this;
    }
}
