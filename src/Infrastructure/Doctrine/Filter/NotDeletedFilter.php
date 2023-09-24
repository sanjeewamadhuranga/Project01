<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Filter;

use App\Domain\Document\Interfaces\SoftDeleteable;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;

class NotDeletedFilter extends BsonFilter
{
    /**
     * @return array<string, mixed>
     */
    public function addFilterCriteria(ClassMetadata $class): array
    {
        if (!$class->reflClass->implementsInterface(SoftDeleteable::class)) {
            return [];
        }

        return ['deleted' => ['$in' => [null, false]]];
    }
}
