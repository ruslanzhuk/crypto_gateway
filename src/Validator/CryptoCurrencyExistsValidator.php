<?php

namespace App\Validator;

use App\Repository\CryptoCurrencyRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CryptoCurrencyExistsValidator extends ConstraintValidator
{
    public function __construct(private CryptoCurrencyRepository $cryptoRepo)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var CryptoCurrencyExists $constraint */

        if(!$constraint instanceof CryptoCurrencyExists) {
            throw new UnexpectedTypeException($constraint, CryptoCurrencyExists::class);
        }

        if (!is_string($value)) {
            return;
        }

        if(!$this->cryptoRepo->findOneBy(['code' => $value])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation()
            ;
        }
    }
}
