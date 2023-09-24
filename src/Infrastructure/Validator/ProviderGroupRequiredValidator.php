<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use App\Domain\Document\Provider\Provider;
use App\Infrastructure\Repository\ProviderRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ProviderGroupRequiredValidator extends ConstraintValidator
{
    public function __construct(private readonly ProviderRepository $providerRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProviderGroupRequired) {
            throw new UnexpectedTypeException($constraint, ProviderGroupRequired::class);
        }

        if (!$value instanceof Provider) {
            throw new UnexpectedTypeException(get_debug_type($value), Provider::class);
        }

        if (null !== $value->getGroup() && '' !== $value->getGroup()) {
            return;
        }

        foreach ($this->providerRepository->findByValue($value->getValue()) as $matchingProvider) {
            if ($matchingProvider === $value) {
                continue;
            }

            $this->context->buildViolation('This value should not be blank.')
                ->atPath('group')
                ->addViolation();

            return;
        }
    }
}
