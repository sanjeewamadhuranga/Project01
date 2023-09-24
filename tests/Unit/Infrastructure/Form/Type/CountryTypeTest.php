<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Type;

use App\Domain\Document\Country;
use App\Infrastructure\Form\Type\EnabledCountryType;
use App\Infrastructure\Repository\CountryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\PreloadedExtension;

class CountryTypeTest extends ChoiceTypeTest
{
    private const CODE_SG = 'SG';
    private const CODE_GB = 'GB';

    private CountryRepository&MockObject $countryRepository;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(CountryRepository::class);
        $this->countryRepository->method('findAll')->willReturn($this->getSampleCountries());

        parent::setUp();
    }

    public function testItTranslatesCountries(): void
    {
        $form = $this->factory->create(static::getTestedType(), null, ['choice_translation_locale' => 'es']);

        self::assertEquals([
            new ChoiceView(self::CODE_SG, self::CODE_SG, 'Singapur'),
            new ChoiceView(self::CODE_GB, self::CODE_GB, 'Reino Unido'),
        ], $form->createView()->vars['choices'] ?? []);
    }

    public function testAvailableChoices(array $choices = []): void
    {
        parent::testAvailableChoices([
            new ChoiceView(self::CODE_SG, self::CODE_SG, 'Singapore'),
            new ChoiceView(self::CODE_GB, self::CODE_GB, 'United Kingdom'),
        ]);
    }

    public function testItSubmitsInvalidValue(mixed $value = 'CZ'): void
    {
        parent::testItSubmitsInvalidValue($value);
    }

    public function testItSubmitsValidValue(mixed $value = self::CODE_SG): void
    {
        parent::testItSubmitsValidValue($value);
    }

    /**
     * @return Country[]
     */
    protected function getSampleCountries(): array
    {
        $singapore = new Country();
        $singapore->setCountryCode(self::CODE_SG);

        $uk = new Country();
        $uk->setCountryCode(self::CODE_GB);

        return [$singapore, $uk];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new EnabledCountryType($this->countryRepository)], []),
        ];
    }

    protected static function getTestedType(): string
    {
        return EnabledCountryType::class;
    }
}
