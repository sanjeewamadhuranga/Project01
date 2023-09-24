<?php

declare(strict_types=1);

namespace App\Application\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EmptyDataDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        return null; // @phpstan-ignore-line
    }

    /**
     * @param mixed[] $context
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_string($data) && '' === trim($data);
    }
}
