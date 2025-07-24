<?php

namespace App\Service;

use App\Entity\Network;
use App\Entity\User;
use App\Entity\Wallet;
use App\Integrations\Wallet\WalletClient;
use Doctrine\ORM\EntityManagerInterface;

class WalletService
{
    public function __construct(private EntityManagerInterface $em, private WalletClient $walletClient)
    {
        $this->em = $em;
        $this->walletClient = $walletClient;
    }

    public function createWallet(Network $network, User $user): Wallet {
        $walletData = $this->walletClient->generateWallet($network->getCode());
        $wallet = new Wallet();
        $wallet->setPublicAddress($walletData["public_address"]);
        $wallet->setPrivateKey($walletData["private_key"]);
        $wallet->setSeedPhrase($walletData["mnemonic"]);
        $wallet->setNetwork($network);
        $wallet->setCreatedAt(new \DateTimeImmutable());
        $wallet->setUser($user);

        $this->em->persist($wallet);
        $this->em->flush();

        return $wallet;
    }

}