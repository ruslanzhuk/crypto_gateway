<?php

namespace App\Validator;

use App\Repository\FiatCurrencyRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class FiatCurrencyExistsValidator extends ConstraintValidator
{
    public function __construct(private FiatCurrencyRepository $fiatRepo)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var FiatCurrencyExists $constraint */

        if (!$constraint instanceof FiatCurrencyExists) {
            throw new UnexpectedTypeException($constraint, FiatCurrencyExists::class);
        }

        if (!is_string($value)) {
            return;
        }

        if (!$this->fiatRepo->findOneBy(['code' => $value])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation()
            ;
        }
    }
}
