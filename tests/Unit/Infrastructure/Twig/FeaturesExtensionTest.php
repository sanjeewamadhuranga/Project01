<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Twig;

use App\Domain\Settings\Features;
use App\Infrastructure\Twig\FeaturesExtension;
use App\Tests\Unit\UnitTestCase;

class FeaturesExtensionTest extends UnitTestCase
{
    public function testItRegistersFunctions(): void
    {
        $features = $this->createStub(Features::class);
        $extension = new FeaturesExtension($features);
        $functions = $extension->getFunctions();

        self::assertCount(2, $functions);

        self::assertSame('has_feature', $functions[0]->getName());
        self::assertSame('kyc_enabled', $functions[1]->getName());
    }
}
