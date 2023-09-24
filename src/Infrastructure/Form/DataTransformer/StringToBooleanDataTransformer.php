<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Converts a boolean value into string: 'true'|'false' for use with CheckboxType and Setting values.
 *
 * @implements DataTransformerInterface<string, bool>
 */
class StringToBooleanDataTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): bool
    {
        return 'true' === $value;
    }

    public function reverseTransform(mixed $value): string
    {
        if (null === $value) {
            return 'false';
        }

        return $value ? 'true' : 'false';
    }
}
