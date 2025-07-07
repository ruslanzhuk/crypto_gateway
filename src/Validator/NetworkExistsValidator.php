<?php

namespace App\Validator;

use App\Repository\NetworkRepository;
use UnexpectedValueException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class NetworkExistsValidator extends ConstraintValidator
{
    public function __construct(private NetworkRepository $networkRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var NetworkExists $constraint */

        if(!$constraint instanceof NetworkExists){
            throw new UnexpectedTypeException($constraint, NetworkExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $network = $this->networkRepository->findOneBy(['name' => $value]);

        // TODO: implement the validation here
        if(!$network) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation()
            ;
        }

    }
}
