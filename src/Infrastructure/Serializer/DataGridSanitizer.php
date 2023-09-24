<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DataGridSanitizer implements DenormalizerAwareInterface, DenormalizerInterface
{
    use DenormalizerAwareTrait;

    final public const SANITIZE_INPUT = 'SANITIZE_INPUT';

    /**
     * @param array<mixed, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $context[self::SANITIZE_INPUT] ?? false;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        $context[self::SANITIZE_INPUT] = false;

        return $this->denormalizer->denormalize($this->sanitize($data), $type, $format, $context);
    }

    /**
     * @param array<mixed, mixed> $haystack
     *
     * @return array<mixed, mixed>
     */
    private function sanitize(array $haystack): array
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = $this->sanitize($value);
            }

            if (is_string($haystack[$key])) {
                $haystack[$key] = trim($haystack[$key]);

                if ('true' === $haystack[$key]) {
                    $haystack[$key] = true;
                }

                if ('false' === $haystack[$key]) {
                    $haystack[$key] = false;
                }
            }

            if (null === $haystack[$key] || '' === $haystack[$key]) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }
}
