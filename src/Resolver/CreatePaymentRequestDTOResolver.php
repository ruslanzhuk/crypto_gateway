<?php

namespace App\Resolver;

use App\Dtos\CreatePaymentRequestDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreatePaymentRequestDTOResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    // The basic logic - deserialization and validation
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // Check the type of argument here
        if ($argument->getType() !== CreatePaymentRequestDTO::class) {
            return [];
        }

        $content = $request->getContent();

        if (empty($content)) {
            throw new BadRequestHttpException('Empty request body.');
        }

        try {
            $dto = $this->serializer->deserialize($content, CreatePaymentRequestDTO::class, 'json');
        } catch (\Throwable $e) {
            throw new BadRequestHttpException('Invalid JSON format.');
        }

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new BadRequestHttpException(json_encode([
                'status' => 'validation_error',
                'errors' => $errorMessages,
            ]));
        }

        yield $dto;
    }
}