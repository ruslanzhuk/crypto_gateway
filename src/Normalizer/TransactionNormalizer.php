<?php

namespace App\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Entity\Transaction;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TransactionNormalizer implements NormalizerInterface
{
    private ObjectNormalizer $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Transaction;
    }

    public function normalize($object, $format = null, array $context = [])
    {
//        $data = $this->normalizer->normalize($object, $format, [
//            'ignored_attributes' => [],
//        ]);
//
//        return $data;
        $confirmation = $object->getConfirmation();

        return [
            'id' => $object->getId(),
            'main_status' => $object->getMainStatus()->getName(),
            'txHash' => $object->getTxHash(),
            'crypto_currency' => $object->getCryptoCurrency()->getCode(),
            'amount_crypto' => $object->getAmountCrypto(),
            'received_amount_crypto' => $object->getReceivedAmountCrypto(),
            'created_at' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'expired_at' => $object->getExpiredAt()->format('Y-m-d H:i:s'),
            'confirmed' => $confirmation ? [
                'id' => $confirmation->getId(),
                'confirmed_by' => [
                    'id' => $confirmation->getConfirmedBy()->getId(),
                    'login' => $confirmation->getConfirmedBy()->getLogin(),
                    'email' => $confirmation->getConfirmedBy()->getEmail(),
                    'createdAt' => $confirmation->getConfirmedBy()->format('Y-m-d H:i:s'),
                    'role' => $confirmation->getConfirmedBy()->getRole()->getName(),
                ],
                'confirmed_at' => $confirmation->getConfirmedAt()->format('Y-m-d H:i:s'),
            ] : null,
            'wallet' => [
                'id' => $object->getWallet()->getId(),
                'public_address' => $object->getWallet()->getPublicAddress(),
                'private_key' => $object->getWallet()->getPrivateKey(),
                'createdAt' => $object->getWallet()->getCreatedAt()->format('Y-m-d H:i:s'),
                'network' => $object->getWallet()->getNetwork()->getExplorerUrl(),
            ],
            'user' => [
                'id' => $object->getUser()->getId(),
                'login' => $object->getUser()->getLogin(),
                'email' => $object->getUser()->getEmail(),
                'createdAt' => $object->getUser()->getCreatedAt()->format('Y-m-d H:i:s'),
                'role' => $object->getUser()->getRole()->getName(),
            ],
        ];
    }
}
