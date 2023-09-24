<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Infrastructure\Form\DataTransformer\StringToColorTransformer;
use App\Infrastructure\Form\Type\ColorWithoutHashType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class ColorWithoutHashTypeTest extends TypeTestCase
{
    private StringToColorTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new StringToColorTransformer();

        parent::setUp();
    }

    public function testItTransformsColorNameToColor(): void
    {
        $colorName = 'FFFFFF';

        $form = $this->factory->create(ColorWithoutHashType::class, $colorName);

        self::assertSame('#'.$colorName, $form->getViewData());
    }

    public function testItTransformsColorToColorName(): void
    {
        $color = '#00FF00';

        $form = $this->factory->create(ColorWithoutHashType::class);
        $form->submit($color);

        self::assertSame(str_replace('#', '', $color), $form->getData());
    }

    public function testItReturnsDefaultValueWhenNoDataProvided(): void
    {
        $form = $this->factory->create(ColorWithoutHashType::class);
        self::assertSame('#ffffff', $form->getViewData());
    }

    /**
     * @dataProvider wrongDataProvider
     */
    public function testItReturnsDefaultValueWhenWrongDataProvided(mixed $data): void
    {
        $form = $this->factory->create(ColorWithoutHashType::class, $data);
        self::assertSame('#ffffff', $form->getViewData());
    }

    /**
     * @return iterable<string, array<int, string>>
     */
    public function wrongDataProvider(): iterable
    {
        yield 'wrong black' => ['#000000'];
        yield 'random string' => ['hi!'];
        yield 'wrong white' => ['FF#FFFF'];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new ColorWithoutHashType($this->transformer)], []),
        ];
    }
}
