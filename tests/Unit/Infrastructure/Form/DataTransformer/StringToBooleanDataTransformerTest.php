<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\DataTransformer;

use App\Infrastructure\Form\DataTransformer\StringToBooleanDataTransformer;
use App\Tests\Unit\UnitTestCase;

class StringToBooleanDataTransformerTest extends UnitTestCase
{
    /**
     * @dataProvider transformProvider
     */
    public function testTransform(string $value, bool $expected): void
    {
        $transformer = new StringToBooleanDataTransformer();
        self::assertSame($expected, $transformer->transform($value));
    }

    /**
     * @dataProvider reverseTransformProvide
     */
    public function testReverseTransform(?bool $value, string $expected): void
    {
        $transformer = new StringToBooleanDataTransformer();
        self::assertSame($expected, $transformer->reverseTransform($value));
    }

    /**
     * @return array<int, array<int, string|bool>>
     */
    private function transformProvider(): array
    {
        return [['true', true], ['false', false], ['null', false]];
    }

    /**
     * @return array<int, array<int, string|bool|null>>
     */
    public function reverseTransformProvide(): array
    {
        return [[true, 'true'], [false, 'false'], [null, 'false']];
    }
}
