<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Upload;

use App\Domain\Document\Interfaces\CompanyAware;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Util\Transliterator;

/**
 * @implements NamerInterface<CompanyAware>
 */
class CompanyFileNamer implements NamerInterface
{
    public function __construct(private readonly Transliterator $transliterator)
    {
    }

    public function name($object, PropertyMapping $mapping): string
    {
        $file = $mapping->getFile($object);

        if (null === $file) {
            return '';
        }

        $originalName = $this->transliterator->transliterate($file->getClientOriginalName(), '_');

        return sprintf('%s_%d_%s', $object->getCompany()->getId(), time(), $originalName);
    }
}
