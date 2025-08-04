<?php

namespace App\Service;

use App\Entity\Network;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;
use App\Integrations\WalletService\WalletServiceConnector;
use Doctrine\ORM\EntityManagerInterface;

class WalletService
{
    private EntityManagerInterface $em;
    private WalletServiceConnector $walletConnector;
    public function __construct(EntityManagerInterface $em, WalletServiceConnector $walletConnector) #private WalletClient $walletClient
    {
        $this->em = $em;
        $this->walletConnector = $walletConnector;
    }

    public function createWallet(Network $network, User $user): Wallet {
        $walletData = $this->walletConnector->generateWallet($network->getCode());
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

    public function updateWallet(Wallet $wallet, array $data): void
    {
        $allowedFields = ['user_id'];
        $invalidFields = array_diff(array_keys($data), $allowedFields);

        if (!empty($invalidFields)) {
            throw new \InvalidArgumentException('Only user_id can be updated. Invalid fields: ' . implode(', ', $invalidFields));
        }

        if (!isset($data['user_id'])) {
            throw new \InvalidArgumentException('User ID is required to update wallet.');
        }

        $user = $this->em->getRepository(User::class)->find($data['user_id']);
        if (!$user) {
            throw new \InvalidArgumentException('User not found.');
        }

        $wallet->setUser($user);
        $this->em->flush();
    }

    public function getLinkedTransaction(Wallet $wallet): ?Transaction {
        return $this->em->getRepository(Transaction::class)->findOneBy(['wallet' => $wallet]);
    }

    public function deleteWallet(Wallet $wallet, bool $force = false): void
    {
        $linkedTx = $this->getLinkedTransaction($wallet);

        if ($linkedTx && !$force) {
            throw new \RuntimeException(sprintf(
                'Wallet is linked to transaction with hash %s. Deletion not allowed without force.',
                $linkedTx->getTxHash()
            ));
        }

        if ($linkedTx && $force) {
            $this->em->remove($linkedTx);
        }

        $this->em->remove($wallet);
        $this->em->flush();
    }

}