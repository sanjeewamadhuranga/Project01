<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Infrastructure\Form\Type\BooleanType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

class BooleanTypeTest extends ChoiceTypeTest
{
    public function testAvailableChoices(array $choices = []): void
    {
        parent::testAvailableChoices([
            new ChoiceView('1', '1', 'boolean.yes'),
            new ChoiceView('0', '0', 'boolean.no'),
        ]);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'es'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = '1'): void
    {
        $form = $this->factory->create(static::getTestedType());
        $form->submit($value);

        self::assertTrue($form->getData());
        self::assertSame($value, $form->getNormData());
        self::assertSame($value, $form->getViewData());
    }

    public function testItConvertsToBoolean(): void
    {
        $form = $this->factory->create(static::getTestedType());
        $form->submit('0');

        self::assertFalse($form->getData());
        self::assertSame('0', $form->getNormData());
        self::assertSame('0', $form->getViewData());
    }

    protected static function getTestedType(): string
    {
        return BooleanType::class;
    }
}
