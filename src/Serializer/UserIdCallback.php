<?php

namespace App\Serializer;

use App\Entity\User;

class UserIdCallback
{
    public function __invoke(?User $user): ?string
    {
        return $user?->getId();
    }
}