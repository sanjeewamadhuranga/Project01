<?php

declare(strict_types=1);

namespace App\Application\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotInArrayValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotInArray) {
            throw new UnexpectedTypeException($constraint, NotInArray::class);
        }

        if (null === $value) {
            return;
        }

        if (in_array($value, $constraint->choices, true)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
