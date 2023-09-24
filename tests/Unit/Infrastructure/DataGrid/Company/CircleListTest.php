<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Company;

use App\Domain\Document\Circles;
use App\Infrastructure\DataGrid\Company\CircleList;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;

class CircleListTest extends UnitTestCase
{
    public function testItTransformsCircleIntoArray(): void
    {
        $id = '61f0213b6c0d85172231b50a';
        $name = 'circle name';
        $description = 'circle descritpion';
        $merchantCollection = new ArrayCollection([1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 11, 12, 13, 14, 15, 16, 17, 18]);

        $circle = $this->createStub(Circles::class);
        $circle->method('getId')->willReturn($id);
        $circle->method('getName')->willReturn($name);
        $circle->method('getDescription')->willReturn($description);
        $circle->method('getCompanies')->willReturn($merchantCollection);

        $circleList = new CircleList($this->createStub(DocumentManager::class));

        self::assertSame([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'merchant_count' => $merchantCollection->count(),
        ], $circleList->transform($circle, 0));
    }
}
