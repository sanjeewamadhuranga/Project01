<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration\Setting;

use App\Infrastructure\Form\DataTransformer\StringToBooleanDataTransformer;
use App\Infrastructure\Form\Type\BooleanStringType;

class BooleanStringTypeTest extends BaseSystemSettingTest
{
    public function testItAddsStringToBooleanTransformer(): void
    {
        $form = $this->factory->create(BooleanStringType::class);

        self::assertInstanceOf(StringToBooleanDataTransformer::class, $form->getConfig()->getModelTransformers()[0]);
    }
}
