<?php

namespace App\Entity;

use App\Repository\NetworkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use App\Entity\Wallet;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NetworkRepository::class)]
class Network
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, CryptoCurrency>
     */

    #[ORM\OneToMany(mappedBy: 'network', targetEntity: CryptoCurrency::class)]
    private Collection $cryptoCurrencies;

    /**
     * @var Collection<int, Wallet>
     */
    #[ORM\OneToMany(targetEntity: Wallet::class, mappedBy: 'network_id')]
    private Collection $wallets;

    #[ORM\Column(length: 20)]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $explorer_url = null;

    public function __construct()
    {
        $this->cryptoCurrencies = new ArrayCollection();
        $this->wallets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CryptoCurrency>
     */
    public function getCryptoCurrencies(): Collection
    {
        return $this->cryptoCurrencies;
    }

    public function addCryptoCurrency(CryptoCurrency $cryptoCurrency): static
    {
        if (!$this->cryptoCurrencies->contains($cryptoCurrency)) {
            $this->cryptoCurrencies[] = $cryptoCurrency;
            $cryptoCurrency->setNetwork($this);
        }

        return $this;
    }

    public function removeCryptoCurrency(CryptoCurrency $cryptoCurrency): static
    {
        if ($this->cryptoCurrencies->removeElement($cryptoCurrency)) {
            if ($cryptoCurrency->getNetwork() === $this) {
                $cryptoCurrency->setNetwork(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Wallet>
     */
    public function getWallets(): Collection
    {
        return $this->wallets;
    }

    public function addWallet(Wallet $wallet): static
    {
        if (!$this->wallets->contains($wallet)) {
            $this->wallets->add($wallet);
            $wallet->setNetwork($this);
        }

        return $this;
    }

    public function removeWallet(Wallet $wallet): static
    {
        if ($this->wallets->removeElement($wallet)) {
            if ($wallet->getNetwork() === $this) {
                $wallet->setNetwork(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getExplorerUrl(): ?string
    {
        return $this->explorer_url;
    }

    public function setExplorerUrl(string $explorer_url): static
    {
        $this->explorer_url = $explorer_url;

        return $this;
    }
}
