<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Passes the values trough array_values.
 *
 * @implements DataTransformerInterface<array<mixed>, array<mixed>>
 */
class ArrayValuesModelTransformer implements DataTransformerInterface
{
    /**
     * @return array<mixed>
     */
    public function transform(mixed $value): array
    {
        return null === $value ? [] : array_values($value);
    }

    /**
     * @return array<mixed>
     */
    public function reverseTransform(mixed $value): array
    {
        return null === $value ? [] : array_values($value);
    }
}
