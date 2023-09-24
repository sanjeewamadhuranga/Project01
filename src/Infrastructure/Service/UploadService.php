<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Metadata\MetadataReader;
use Vich\UploaderBundle\Util\ClassUtils;

class UploadService
{
    public function __construct(private readonly MetadataReader $metadata, private readonly UploadHandler $handler)
    {
    }

    public function upload(object $object, ?string $mapping = null): void
    {
        foreach ($this->getUploadableFields($object, $mapping) as $field) {
            $this->handler->upload($object, $field);
        }
    }

    /**
     * @return string[]
     */
    protected function getUploadableFields(object $object, ?string $mapping = null): array
    {
        $fields = $this->metadata->getUploadableFields(ClassUtils::getClass($object), $mapping);

        return \array_map(static fn (array $data): string => $data['propertyName'], $fields);
    }
}
