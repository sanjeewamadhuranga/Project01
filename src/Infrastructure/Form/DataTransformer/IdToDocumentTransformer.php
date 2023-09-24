<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\DataTransformer;

use App\Domain\Document\BaseDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<BaseDocument|BaseDocument[], string|string[]>
 */
class IdToDocumentTransformer implements DataTransformerInterface
{
    /**
     * @param class-string<BaseDocument> $documentClass
     */
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly string $documentClass,
        private readonly bool $multiple = false,
    ) {
    }

    /**
     * @return string|string[]|null
     */
    public function transform(mixed $value): string|array|null
    {
        if ($this->multiple) {
            $data = [];

            if (is_iterable($value)) {
                foreach ($value as $object) {
                    $data[] = (string) $object->getId();
                }
            }

            return $data;
        }

        if (!$value instanceof $this->documentClass) {
            return null;
        }

        return (string) $value->getId();
    }

    /**
     * @return BaseDocument|BaseDocument[]|null
     */
    public function reverseTransform(mixed $value): BaseDocument|array|null
    {
        $dm = $this->managerRegistry->getManagerForClass($this->documentClass);
        assert($dm instanceof DocumentManager);

        if ($this->multiple) {
            if (!is_array($value)) {
                return [];
            }

            // @phpstan-ignore-next-line
            return $dm->createQueryBuilder($this->documentClass)->field('id')->in($value)->getQuery()->execute();
        }

        $document = $dm->getRepository($this->documentClass)->find($value);

        if (null === $document) {
            // @phpstan-ignore-next-line
            throw new TransformationFailedException(sprintf('A document %s with id "%s" does not exist!', $this->documentClass, $value));
        }

        return $document;
    }
}
