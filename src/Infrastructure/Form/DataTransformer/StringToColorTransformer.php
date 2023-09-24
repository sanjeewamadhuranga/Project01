<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements DataTransformerInterface<string, string>
 */
class StringToColorTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): string
    {
        if (null === $value || 6 !== strlen($value)) {
            return '#ffffff';
        }

        return '#'.str_replace('#', '', $value);
    }

    public function reverseTransform(mixed $value): string
    {
        return str_replace('#', '', (string) $value);
    }
}
