<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Configuration\MdrBillingType;
use App\Infrastructure\Form\Type\EnabledCurrencyType;
use App\Infrastructure\Form\Type\MdrCodeType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class MdrBillingTypeTest extends TypeTestCase
{
    private SystemSettings&Stub $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);
        $this->settings->method('getEnabledCurrencies')->willReturn(['USD', 'EURO', 'GBP']);
        $this->settings->method('getMdrCodes')->willReturn(['AAA', 'BBB', 'CCC']);

        parent::setUp();
    }

    public function testThereIsNoMdrFieldWhenValidationGroupsDontContainsCreate(): void
    {
        $form = $this->factory->create(MdrBillingType::class);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Child "mdr" does not exist.');

        $form->get('mdr');
    }

    public function testThereIsMdrFieldWhenValidationGroupsContainsCreate(): void
    {
        $form = $this->factory->create(MdrBillingType::class, options: ['validation_groups' => ['create']]);

        self::assertInstanceOf(Form::class, $form->get('mdr'));
    }

    /**
     * @return array<int, PreloadedExtension|ValidatorExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new MdrCodeType($this->settings)], []),
            new PreloadedExtension([new EnabledCurrencyType($this->settings)], []),
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
