<?php

namespace App\Entity;

use App\Repository\TelegramBotChatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TelegramBotChatRepository::class)]
class TelegramBotChat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $chatId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'telegramBotChats')]
    private ?TelegramBotIntegration $integration = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): static
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getIntegration(): ?TelegramBotIntegration
    {
        return $this->integration;
    }

    public function setIntegration(?TelegramBotIntegration $integration): static
    {
        $this->integration = $integration;

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
}
