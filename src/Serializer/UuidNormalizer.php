<?php


namespace App\Serializer;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UuidNormalizer implements NormalizerInterface
{
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof UuidInterface;
    }

    public function normalize($object, $format = null, array $context = []): string
    {
        return $object->toString();
    }
}