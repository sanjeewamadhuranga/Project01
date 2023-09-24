<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Doctrine\Filter;

use App\Infrastructure\Doctrine\Filter\NotDeletedFilter;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use ReflectionClass;

class NotDeletedFilterTest extends UnitTestCase
{
    private NotDeletedFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new NotDeletedFilter($this->createStub(DocumentManager::class));

        parent::setUp();
    }

    public function testItReturnEmptyArrayWhenClassDoesNotImplementsSoftDeletable(): void
    {
        $classMetadata = $this->getClassMetadata(false);
        self::assertSame([], $this->filter->addFilterCriteria($classMetadata));
    }

    public function testItReturnsArrayWhenClassImplementsSoftDeletable(): void
    {
        $classMetadata = $this->getClassMetadata(true);
        self::assertSame(['deleted' => ['$in' => [null, false]]], $this->filter->addFilterCriteria($classMetadata));
    }

    private function getClassMetadata(bool $implementsSoftDeletable): ClassMetadata
    {
        $reflection = $this->createStub(ReflectionClass::class);
        $reflection->method('implementsInterface')->willReturn($implementsSoftDeletable);

        $classMetadata = $this->createStub(ClassMetadata::class);
        $classMetadata->reflClass = $reflection;

        return $classMetadata;
    }
}
