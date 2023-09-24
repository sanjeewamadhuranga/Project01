<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Validator;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Validator\EnabledCurrency;
use App\Infrastructure\Validator\EnabledCurrencyValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<EnabledCurrencyValidator>
 */
class EnabledCurrencyValidatorTest extends ConstraintValidatorTestCase
{
    public function testItBuildsViolationWhenCurrencyIsNotEnabled(): void
    {
        $currency = 'GBP';
        $constraint = new EnabledCurrency();

        $this->validator->validate($currency, $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    public function testItDoesNotBuildViolationWhenCurrencyIsEnabled(): void
    {
        $currency = 'USD';

        $this->validator->validate($currency, new EnabledCurrency());

        $this->assertNoViolation();
    }

    protected function createValidator(): EnabledCurrencyValidator
    {
        $systemSettings = $this->createStub(SystemSettings::class);
        $systemSettings->method('getEnabledCurrencies')->willReturn(['USD' => 'USD', 'EURO' => 'EURO']);

        return new EnabledCurrencyValidator($systemSettings);
    }
}
