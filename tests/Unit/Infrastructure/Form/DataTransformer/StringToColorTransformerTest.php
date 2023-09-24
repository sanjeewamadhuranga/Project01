<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\DataTransformer;

use App\Infrastructure\Form\DataTransformer\StringToColorTransformer;
use App\Tests\Unit\UnitTestCase;

class StringToColorTransformerTest extends UnitTestCase
{
    private StringToColorTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new StringToColorTransformer();

        parent::setUp();
    }

    /**
     * @dataProvider colorNamesDataProvider
     */
    public function testItTransformsStringToColor(string $colorName): void
    {
        $color = $this->transformer->transform($colorName);

        self::assertSame('#', $color[0]);
        self::assertSame(1, substr_count($color, '#'));
        self::assertSame(7, strlen($color));
    }

    /**
     * @dataProvider wrongColorNamesDataProvider
     */
    public function testItDefaultsToWhiteWhenWrongValueProvided(string $colorName): void
    {
        self::assertSame('#ffffff', $this->transformer->transform($colorName));
    }

    public function testThatDefaultColorIsWhite(): void
    {
        self::assertSame('#ffffff', $this->transformer->transform(null));
    }

    /**
     * @dataProvider colorDataProvider
     */
    public function testItRemovesHashDuringReverseTransformation(string $color): void
    {
        $colorName = $this->transformer->reverseTransform($color);

        self::assertSame(0, substr_count($colorName, '#'));
        self::assertSame(6, strlen($colorName));
    }

    /**
     * @return iterable<string, array<int, string>>
     */
    public function colorNamesDataProvider(): iterable
    {
        yield 'black' => ['000000'];
        yield 'white' => ['FFFFFF'];
    }

    /**
     * @return iterable<string, array<int, mixed>>
     */
    public function wrongColorNamesDataProvider(): iterable
    {
        yield 'wrong white' => ['FF#FFFF'];
        yield 'green with hash' => ['#00FF00'];
        yield 'some string' => ['someOtherValue'];
    }

    /**
     * @return iterable<string, array<int, string>>
     */
    public function colorDataProvider(): iterable
    {
        yield 'red' => ['#FF0000'];
        yield 'white' => ['#FFFFFF'];
        yield 'wrong white' => ['FF#FFFF'];
    }
}
