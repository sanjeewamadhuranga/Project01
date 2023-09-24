<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Company\Address;

use App\Domain\Settings\Branding;
use App\Domain\Settings\SystemSettings;
use App\Domain\Settings\Theme;
use App\Infrastructure\Form\Company\Address\AddressType;
use App\Infrastructure\Form\Company\Address\StateType;
use App\Infrastructure\Form\Type\EnabledCountryType;
use App\Infrastructure\Repository\CountryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class AddressTypeTest extends TypeTestCase
{
    private CountryRepository&MockObject $countryRepository;

    private SystemSettings&MockObject $settings;

    protected function setUp(): void
    {
        $this->countryRepository = $this->createMock(CountryRepository::class);
        $this->countryRepository->method('findAll')->willReturn([]);
        $this->settings = $this->createMock(SystemSettings::class);
        parent::setUp();
    }

    public function testItShowsOnlyEnabledCountriesIfOptionIsPassed(): void
    {
        $this->settings->method('getValue')->with(SystemSettings::ADMIN_THEME, self::isType('string'))->willReturn(Theme::DIALOG);
        $form = $this->factory->create(AddressType::class, null, ['onlyEnabledCountries' => true]);
        self::assertInstanceOf(EnabledCountryType::class, $form->get('country')->getConfig()->getType()->getInnerType());
    }

    public function testItShowsAllCountriesIfOptionIsNotPassed(): void
    {
        $form = $this->factory->create(AddressType::class);
        self::assertInstanceOf(CountryType::class, $form->get('country')->getConfig()->getType()->getInnerType());
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new EnabledCountryType($this->countryRepository)], []),
            new PreloadedExtension([new AddressType(new Branding($this->settings))], []),
            new PreloadedExtension([new StateType(new Branding($this->settings))], []),
        ];
    }
}
