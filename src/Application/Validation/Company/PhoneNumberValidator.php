<?php

declare(strict_types=1);

namespace App\Application\Validation\Company;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PhoneNumberValidator extends ConstraintValidator
{
    // According to E164 standard, the number should be up to 15 digits long and start with a + sign.
    private const PHONE_REGEX = '/^\+[1-9]\d{3,14}$/';

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PhoneNumber) {
            throw new UnexpectedTypeException($constraint, PhoneNumber::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (0 >= preg_match(self::PHONE_REGEX, $value, $matches)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
