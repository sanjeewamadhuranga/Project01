<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\EnabledCurrencyType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;

class CurrencyTypeTest extends ChoiceTypeTest
{
    private const CODE_SGD = 'SGD';
    private const CODE_GBP = 'GBP';

    private SystemSettings&Stub $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);
        $this->settings->method('getEnabledCurrencies')->willReturn($this->getTestCurrencies());

        parent::setUp();
    }

    public function testAvailableChoices(array $choices = []): void
    {
        parent::testAvailableChoices([
            new ChoiceView(self::CODE_GBP, self::CODE_GBP, self::CODE_GBP),
            new ChoiceView(self::CODE_SGD, self::CODE_SGD, self::CODE_SGD),
        ]);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'ZZZ'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = self::CODE_SGD): void
    {
        parent::testItSubmitsValidValue($value);
    }

    /**
     * @return array<string, string>
     */
    protected function getTestCurrencies(): array
    {
        return [
            'GBP' => 'GBP',
            'SGD' => 'SGD',
        ];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new EnabledCurrencyType($this->settings)], []),
        ];
    }

    protected static function getTestedType(): string
    {
        return EnabledCurrencyType::class;
    }
}
