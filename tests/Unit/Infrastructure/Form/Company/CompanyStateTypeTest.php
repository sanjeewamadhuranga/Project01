<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Company;

use App\Domain\Company\SriLankaStates;
use App\Domain\Settings\Branding;
use App\Domain\Settings\SystemSettings;
use App\Domain\Settings\Theme;
use App\Infrastructure\Form\Company\Address\StateType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class CompanyStateTypeTest extends TypeTestCase
{
    private SystemSettings&MockObject $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createMock(SystemSettings::class);

        parent::setUp();
    }

    public function testParentIsTextFieldWhenThemeIsNotDialog(): void
    {
        $this->settings->method('getValue')->with(SystemSettings::ADMIN_THEME, self::isType('string'))->willReturn(Theme::BML);

        $form = $this->factory->create(StateType::class);

        $parent = $form->getConfig()->getType()->getParent();
        self::assertNotNull($parent);
        self::assertInstanceOf(TextType::class, $parent->getInnerType());
    }

    public function testParentIsChoiceFieldWithSriLankaStatesWhenThemeIsDialog(): void
    {
        $this->settings->method('getValue')->with(SystemSettings::ADMIN_THEME, self::isType('string'))->willReturn(Theme::DIALOG);

        $form = $this->factory->create(StateType::class);

        $parent = $form->getConfig()->getType()->getParent();
        self::assertNotNull($parent);
        self::assertInstanceOf(ChoiceType::class, $parent->getInnerType());
        self::assertSame(array_combine(SriLankaStates::getStates(), SriLankaStates::getStates()), $form->getConfig()->getOption('choices'));
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new StateType(new Branding($this->settings))], []),
        ];
    }
}
