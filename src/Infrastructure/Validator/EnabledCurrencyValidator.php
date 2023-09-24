<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Domain\Settings\SystemSettings;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EnabledCurrencyValidator extends ConstraintValidator
{
    public function __construct(private readonly SystemSettings $systemSettings)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        $currencies = array_keys($this->systemSettings->getEnabledCurrencies());

        if (!$constraint instanceof EnabledCurrency) {
            throw new UnexpectedTypeException($constraint, EnabledCurrency::class);
        }

        if (in_array($value, $currencies, true)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
