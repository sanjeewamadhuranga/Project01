<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Type\LanguageType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;

class LanguageTypeTest extends ChoiceTypeTest
{
    private const EN = 'en';
    private const VN = 'vn';

    private SystemSettings&Stub $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);
        $this->settings->method('getEnabledLanguages')->willReturn($this->getSampleLanguages());

        parent::setUp();
    }

    public function testAvailableChoices(array $choices = []): void
    {
        parent::testAvailableChoices([
            new ChoiceView(self::EN, self::EN, 'English'),
            new ChoiceView(self::VN, self::VN, 'Vietnamese'),
        ]);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'es'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = self::VN): void
    {
        parent::testItSubmitsValidValue($value);
    }

    /**
     * @return array<string, string>
     */
    protected function getSampleLanguages(): array
    {
        return [
            self::EN => 'English',
            self::VN => 'Vietnamese',
        ];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new LanguageType($this->settings)], []),
        ];
    }

    protected static function getTestedType(): string
    {
        return LanguageType::class;
    }
}
