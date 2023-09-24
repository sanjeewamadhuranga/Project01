<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Test\TypeTestCase;

abstract class ChoiceTypeTest extends TypeTestCase
{
    public function testItSubmitsInvalidValue(mixed $value): void
    {
        $form = $this->factory->create(static::getTestedType());
        $form->submit($value);

        self::assertNull($form->getData());
        self::assertNull($form->getNormData());
        self::assertSame($value, $form->getViewData());
    }

    public function testSubmitNull(mixed $expected = null, mixed $norm = null, mixed $view = ''): void
    {
        $form = $this->factory->create(static::getTestedType());
        $form->submit(null);

        self::assertSame($expected, $form->getData());
        self::assertSame($norm, $form->getNormData());
        self::assertSame($view, $form->getViewData());
    }

    public function testItSubmitsValidValue(mixed $value): void
    {
        $form = $this->factory->create(static::getTestedType());
        $form->submit($value);

        self::assertSame($value, $form->getData());
        self::assertSame($value, $form->getNormData());
        self::assertSame(is_numeric($value) ? (string) $value : $value, $form->getViewData());
    }

    /**
     * @param ChoiceView[] $choices
     */
    public function testAvailableChoices(array $choices = []): void
    {
        $form = $this->factory->create(static::getTestedType());

        self::assertEquals($choices, $form->createView()->vars['choices'] ?? []);
    }

    abstract protected static function getTestedType(): string;
}
