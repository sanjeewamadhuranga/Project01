<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Domain\Settings\Features;
use App\Infrastructure\Form\Type\EnabledFeatureType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;

class FeatureTypeTest extends ChoiceTypeTest
{
    private const FEATURE_1 = 'feature1';
    private const FEATURE_2 = 'feature2';
    private const FEATURE_3 = 'feature3';
    private const FEATURE_4 = 'feature4';
    private const FEATURE_5 = 'feature5';

    private Features&Stub $features;

    protected function setUp(): void
    {
        $this->features = $this->createStub(Features::class);
        $this->features->method('getEnabledFeatures')->willReturn($this->getTestFeatures());

        parent::setUp();
    }

    public function testAvailableChoices(array $choices = []): void
    {
        parent::testAvailableChoices([
            new ChoiceView(self::FEATURE_1, self::FEATURE_1, self::FEATURE_1),
            new ChoiceView(self::FEATURE_2, self::FEATURE_2, self::FEATURE_2),
            new ChoiceView(self::FEATURE_3, self::FEATURE_3, self::FEATURE_3),
            new ChoiceView(self::FEATURE_4, self::FEATURE_4, self::FEATURE_4),
            new ChoiceView(self::FEATURE_5, self::FEATURE_5, self::FEATURE_5),
        ]);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'ABC'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = self::FEATURE_1): void
    {
        parent::testItSubmitsValidValue($value);
    }

    /**
     * @return string[]
     */
    private function getTestFeatures(): array
    {
        return [self::FEATURE_1, self::FEATURE_2, self::FEATURE_3, self::FEATURE_4, self::FEATURE_5];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new EnabledFeatureType($this->features)], []),
        ];
    }

    protected static function getTestedType(): string
    {
        return EnabledFeatureType::class;
    }
}
