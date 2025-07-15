<?php

namespace App\Resolver;

use App\Service\Provider\Price\PriceProviderInterface;

class PriceProviderResolver
{
    public function __construct(
        /** @var iterable<PriceProviderInterface> */
        private iterable $providers
    ) {}

    public function resolve(string $network): PriceProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($network)) {
                return $provider;
            }
        }

        throw new \RuntimeException("No provider found for network: $network");
    }
}
