<?php

declare(strict_types=1);

namespace App\Application\Serializer\Denormalizer;

use App\Domain\Document\BaseDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DocumentDenormalizer implements DenormalizerInterface
{
    public function __construct(private readonly DocumentManager $dm)
    {
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): BaseDocument
    {
        /** @var class-string<BaseDocument> $type */
        $document = $this->dm->find($type, $data);
        if (null === $document) {
            throw new DocumentNotFoundException(sprintf('Document %s with id %s not found.', $type, $data));
        }

        return $document;
    }

    /**
     * @param array<mixed, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_string($data) && is_a($type, BaseDocument::class, true);
    }
}
