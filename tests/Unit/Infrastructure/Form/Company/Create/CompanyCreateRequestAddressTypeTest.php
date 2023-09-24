<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Company\Create;

use App\Domain\Company\SriLankaStates;
use App\Domain\Settings\Branding;
use App\Domain\Settings\SystemSettings;
use App\Domain\Settings\Theme;
use App\Infrastructure\Form\Company\Address\StateType;
use App\Infrastructure\Form\Company\Create\CompanyCreateRequestAddressType;
use App\Infrastructure\Form\Type\EnabledCountryType;
use App\Infrastructure\Repository\CountryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class CompanyCreateRequestAddressTypeTest extends TypeTestCase
{
    private SystemSettings&MockObject $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createMock(SystemSettings::class);

        parent::setUp();
    }

    public function testAddress2IsMandatoryWhenThemeIsDialog(): void
    {
        $this->settings->method('getValue')->with(SystemSettings::ADMIN_THEME, self::isType('string'))->willReturn(Theme::DIALOG);

        $form = $this->factory->create(CompanyCreateRequestAddressType::class);

        self::assertTrue($form->get('address2')->getConfig()->getOption('required'));
    }

    public function testAddress2IsNotMandatoryWhenThemeIsNotDialog(): void
    {
        $this->settings->method('getValue')->with(SystemSettings::ADMIN_THEME, self::isType('string'))->willReturn(Theme::BML);

        $form = $this->factory->create(CompanyCreateRequestAddressType::class);

        self::assertFalse($form->get('address2')->getConfig()->getOption('required'));
    }

    public function testStateIsDropdownWithSriLankaStatesWhenThemeIsDialog(): void
    {
        $this->settings->method('getValue')->with(SystemSettings::ADMIN_THEME, self::isType('string'))->willReturn(Theme::DIALOG);

        $form = $this->factory->create(CompanyCreateRequestAddressType::class);

        $parent = $form->get('state')->getConfig()->getType()->getParent();
        self::assertNotNull($parent);
        self::assertInstanceOf(ChoiceType::class, $parent->getInnerType());
        self::assertSame(array_combine(SriLankaStates::getStates(), SriLankaStates::getStates()), $form->get('state')->getConfig()->getOption('choices'));
    }

    public function testStateIsTextFieldWhenThemeIsNotDialog(): void
    {
        $this->settings->method('getValue')->with(SystemSettings::ADMIN_THEME, self::isType('string'))->willReturn(Theme::HDBANK);

        $form = $this->factory->create(CompanyCreateRequestAddressType::class);

        $parent = $form->get('state')->getConfig()->getType()->getParent();
        self::assertNotNull($parent);
        self::assertInstanceOf(TextType::class, $parent->getInnerType());
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new CompanyCreateRequestAddressType(new Branding($this->settings))], []),
            new PreloadedExtension([new StateType(new Branding($this->settings))], []),
            new PreloadedExtension([new EnabledCountryType($this->createStub(CountryRepository::class))], []),
        ];
    }
}
