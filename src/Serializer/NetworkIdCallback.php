<?php

namespace App\Serializer;

use App\Entity\Network;

class NetworkIdCallback
{
    public function __invoke(?Network $network): ?int
    {
        return $network?->getId();
    }
}