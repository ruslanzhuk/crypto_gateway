<?php

namespace App\Entity;

use App\Repository\FiatCurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiatCurrencyRepository::class)]
class FiatCurrency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $code = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\Column(length: 5)]
    private ?string $symbol = null;

    /**
     * @var Collection<int, FiatAmountPerTransaction>
     */
    #[ORM\OneToMany(targetEntity: FiatAmountPerTransaction::class, mappedBy: 'fiatCurrency')]
    private Collection $fiatAmountPerTransactions;

    public function __construct()
    {
        $this->fiatAmountPerTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return Collection<int, FiatAmountPerTransaction>
     */
    public function getFiatAmountPerTransactions(): Collection
    {
        return $this->fiatAmountPerTransactions;
    }

    public function addFiatAmountPerTransaction(FiatAmountPerTransaction $fiatAmountPerTransaction): static
    {
        if (!$this->fiatAmountPerTransactions->contains($fiatAmountPerTransaction)) {
            $this->fiatAmountPerTransactions->add($fiatAmountPerTransaction);
            $fiatAmountPerTransaction->setFiatCurrency($this);
        }

        return $this;
    }

    public function removeFiatAmountPerTransaction(FiatAmountPerTransaction $fiatAmountPerTransaction): static
    {
        if ($this->fiatAmountPerTransactions->removeElement($fiatAmountPerTransaction)) {
            // set the owning side to null (unless already changed)
            if ($fiatAmountPerTransaction->getFiatCurrency() === $this) {
                $fiatAmountPerTransaction->setFiatCurrency(null);
            }
        }

        return $this;
    }
}
