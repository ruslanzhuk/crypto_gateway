<?php

namespace App\Entity;

use App\Repository\TelegramBotIntegrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TelegramBotIntegrationRepository::class)]
class TelegramBotIntegration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $botToken = null;

    #[ORM\Column(length: 255)]
    private ?string $botName = null;

    #[ORM\Column(length: 255)]
    private ?string $botUsername = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, TelegramBotChat>
     */
    #[ORM\OneToMany(targetEntity: TelegramBotChat::class, mappedBy: 'integration')]
    private Collection $telegramBotChats;

    #[ORM\ManyToOne(inversedBy: 'telegramBots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    public function __construct()
    {
        $this->telegramBotChats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBotToken(): ?string
    {
        return $this->botToken;
    }

    public function setBotToken(string $botToken): static
    {
        $this->botToken = $botToken;

        return $this;
    }

    public function getBotName(): ?string
    {
        return $this->botName;
    }

    public function setBotName(string $botName): static
    {
        $this->botName = $botName;

        return $this;
    }

    public function getBotUsername(): ?string
    {
        return $this->botUsername;
    }

    public function setBotUsername(string $botUsername): static
    {
        $this->botUsername = $botUsername;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
     * @return Collection<int, TelegramBotChat>
     */
    public function getTelegramBotChats(): Collection
    {
        return $this->telegramBotChats;
    }

    public function addTelegramBotChat(TelegramBotChat $telegramBotChat): static
    {
        if (!$this->telegramBotChats->contains($telegramBotChat)) {
            $this->telegramBotChats->add($telegramBotChat);
            $telegramBotChat->setIntegration($this);
        }

        return $this;
    }

    public function removeTelegramBotChat(TelegramBotChat $telegramBotChat): static
    {
        if ($this->telegramBotChats->removeElement($telegramBotChat)) {
            // set the owning side to null (unless already changed)
            if ($telegramBotChat->getIntegration() === $this) {
                $telegramBotChat->setIntegration(null);
            }
        }

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }
}
