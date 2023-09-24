<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Infrastructure\Form\Type\KeyValueType;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Test\TypeTestCase;

class KeyValueTypeTest extends TypeTestCase
{
    public function testItDoNothingWhenNullProvided(): void
    {
        $form = $this->factory->create(KeyValueType::class);

        self::assertNull($form->getData());
    }

    public function testItTranslateArrayToData(): void
    {
        $input = [
            'firstElement' => 'elephant',
            'secondElement' => 'frog',
            'thirdElement' => 'cougar',
        ];

        $form = $this->factory->create(KeyValueType::class, $input);

        $output = [];
        foreach ($input as $key => $item) {
            $output[] = [
                'key' => $key,
                'value' => $item,
            ];
        }

        self::assertSame($output, $form->getData());
    }

    /**
     * @dataProvider valuesForTransformer
     */
    public function testItTransformValue(mixed $value): void
    {
        self::assertSame($value, (new KeyValueType())->transform($value));
    }

    public function testItThrowsExceptionWhenWrongValueForReverseTransformProvided(): void
    {
        self::expectException(TransformationFailedException::class);

        (new KeyValueType())->reverseTransform([['key', 'value']]); // @phpstan-ignore-line
    }

    public function testItReversTransform(): void
    {
        $key1 = 'key1';
        $key2 = 'key2';
        $key3 = 'key3';

        $value1 = 'value1';
        $value2 = 'value2';
        $value3 = 'value2';

        $result = (new KeyValueType())->reverseTransform([
            [
                'key' => $key1,
                'value' => $value1,
            ],
            [
                'key' => $key2,
                'value' => $value2,
            ],
            [
                'key' => $key3,
                'value' => $value3,
            ],
        ]);

        self::assertSame([
            $key1 => $value1,
            $key2 => $value2,
            $key3 => $value3,
        ], $result);
    }

    /**
     * @return iterable<array<int, mixed>>
     */
    public function valuesForTransformer(): iterable
    {
        yield 'string' => ['aaa'];
        yield 'int' => [125];
        yield 'object' => [new stdClass()];
        yield 'null' => [null];
        yield 'array' => [[0, 'sss', null]];
    }
}
