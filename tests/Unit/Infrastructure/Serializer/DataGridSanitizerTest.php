<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Serializer;

use App\Infrastructure\Serializer\DataGridSanitizer;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DataGridSanitizerTest extends UnitTestCase
{
    public function testItIsActiveIfContextOptionIsProvided(): void
    {
        self::assertTrue((new DataGridSanitizer())->supportsDenormalization([], '', null, [DataGridSanitizer::SANITIZE_INPUT => true]));
    }

    public function testItIsInactiveIfContextOptionIsNotProvided(): void
    {
        self::assertFalse((new DataGridSanitizer())->supportsDenormalization([], ''));
    }

    public function testItSanitizeInput(): void
    {
        $innerDenormalizer = $this->createMock(DenormalizerInterface::class);
        $denormalizer = new DataGridSanitizer();
        $denormalizer->setDenormalizer($innerDenormalizer);

        $innerDenormalizer->expects(self::once())->method('denormalize')->with([
            'emptyArray' => [],
            'falseValue' => false,
            'falseString' => false,
            'trueString' => true,
            'zero' => 0,
        ]);

        $denormalizer->denormalize([
            'emptyArray' => [],
            'nullValue' => null,
            'falseValue' => false,
            'falseString' => 'false',
            'trueString' => 'true',
            'emptyString' => '',
            'space' => ' ',
            'zero' => 0,
        ], 'Test', null, [DataGridSanitizer::SANITIZE_INPUT => true]);
    }

    public function testItDenormalizesObjectWithSanitizedData(): void
    {
        $denormalized = (new Serializer([new DataGridSanitizer(), new ObjectNormalizer()]))->denormalize([
            'field1' => ' ',
            'field2' => '',
            'field3' => false,
        ], TestSerializable::class, null, [DataGridSanitizer::SANITIZE_INPUT => true]);

        self::assertNull($denormalized->field1);
        self::assertNull($denormalized->field2);
        self::assertFalse($denormalized->field3);
    }

    public function testItDoesNotSanitizeDataIfContextFlagIsNotSet(): void
    {
        $denormalized = (new Serializer([new DataGridSanitizer(), new ObjectNormalizer()]))->denormalize([
            'field1' => ' ',
            'field2' => '',
            'field3' => false,
        ], TestSerializable::class);

        self::assertSame(' ', $denormalized->field1);
        self::assertSame('', $denormalized->field2);
        self::assertFalse($denormalized->field3);
    }
}

class TestSerializable
{
    public ?string $field1 = null;

    public ?string $field2 = null;

    public ?bool $field3 = null;
}
