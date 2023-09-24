<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Infrastructure\Form\Type\CSVTextAreaType;
use Symfony\Component\Form\Test\TypeTestCase;

class CSVTextAreaTypeTest extends TypeTestCase
{
    public function testItDoNothingWhenNullProvided(): void
    {
        $form = $this->factory->create(CSVTextAreaType::class);
        self::assertNull($form->getData());
    }

    /**
     * @dataProvider transformationDataProvider
     *
     * @param string[]|null $input
     */
    public function testItTransform(?array $input, ?string $expectedOutput): void
    {
        self::assertSame($expectedOutput, (new CSVTextAreaType())->transform($input));
    }

    /**
     * @dataProvider reverseTransformDataProvider
     *
     * @param string[]|null $expectedOutput
     */
    public function testItReversTransform(?string $input, ?array $expectedOutput): void
    {
        self::assertSame($expectedOutput, (new CSVTextAreaType())->reverseTransform($input));
    }

    /**
     * @return iterable<string, array{0: string[]|null, 1: string|null}>
     */
    public function transformationDataProvider(): iterable
    {
        yield 'empty array' => [[], null];
        yield 'null' => [null, null];
        yield 'string array' => [['test1', 'test2'], 'test1,test2'];
    }

    /**
     * @return iterable<string, array{0: string|null, 1: string[]|null}>
     */
    public function reverseTransformDataProvider(): iterable
    {
        yield 'empty string' => ['', []];
        yield 'null' => [null, null];
        yield 'csv' => ['test1,test2', ['test1', 'test2']];
        yield 'csv with spaces' => ['te st1, test2 ', ['te st1', 'test2']];
        yield 'csv with space and new lines' => ["te st1, \r\nte\r\nst2 ", ['te st1', 'test2']];
    }

    public function testItConvertsInputToArray(): void
    {
        $input = 'test1,test2,test3';
        $form = $this->factory->create(self::getTestedType());
        $form->submit($input);

        self::assertSame(['test1', 'test2', 'test3'], $form->getData());
        self::assertSame($input, $form->getNormData());
    }

    public function testItConvertsModelDataToString(): void
    {
        $modelData = ['test1', 'test2', 'test3'];
        $form = $this->factory->create(self::getTestedType());
        $form->setData($modelData);

        self::assertSame($modelData, $form->getData());
        self::assertSame(implode(',', $modelData), $form->getNormData());
    }

    protected static function getTestedType(): string
    {
        return CSVTextAreaType::class;
    }
}
