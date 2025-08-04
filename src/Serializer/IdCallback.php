<?php

namespace App\Serializer;

use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;

class IdCallback
{
    public function __construct(private LoggerInterface $logger) {}
    public function __invoke(mixed $id): mixed
    {
        $this->logger->info('ID Type: '.gettype($id), ['class' => is_object($id) ? get_class($id) : null]);

        if ($id instanceof UuidInterface) {
            return $id->toString();
        }

        if (is_object($id) && method_exists($id, '__toString')) {
            return (string) $id;
        }

        return $id;
    }
}
