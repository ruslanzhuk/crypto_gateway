<?php

namespace App\Serializer;

class CircularReferenceHandler
{
    public function __invoke($object, $format = null, array $context = [])
    {
        return method_exists($object, 'getId') ? $object->getId() : spl_object_hash($object);
    }
}