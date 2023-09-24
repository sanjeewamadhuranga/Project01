<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Onboarding;

use App\Infrastructure\Form\Onboarding\BatchDynamicCodeType;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Test\TypeTestCase;

class BatchDynamicCodeTypeTest extends TypeTestCase
{
    public function testItAddsNumberFieldWhenIsNotEdit(): void
    {
        $form = $this->factory->create(BatchDynamicCodeType::class, options: ['isEdit' => false]);

        self::assertInstanceOf(Form::class, $form->get('number'));
        self::assertInstanceOf(Form::class, $form->get('active'));
    }

    public function testItDoNotAddNumberFieldWhenIsEdit(): void
    {
        $form = $this->factory->create(BatchDynamicCodeType::class, options: ['isEdit' => true]);

        self::expectException(OutOfBoundsException::class);
        self::expectExceptionMessage('Child "number" does not exist.');

        $form->get('number');
    }
}
