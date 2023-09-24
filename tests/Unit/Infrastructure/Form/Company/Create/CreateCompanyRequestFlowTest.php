<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Company\Create;

use App\Infrastructure\Form\Company\Create\CompanyCreateRequestAddressType;
use App\Infrastructure\Form\Company\Create\CompanyCreateRequestBasicsType;
use App\Infrastructure\Form\Company\Create\CompanyCreateRequestContactType;
use App\Infrastructure\Form\Company\Create\CompanyCreateRequestPaymentsType;
use App\Infrastructure\Form\Company\Create\CompanyCreateRequestSettingsType;
use App\Infrastructure\Form\Company\Create\CreateCompanyRequestFlow;
use App\Tests\Unit\UnitTestCase;

class CreateCompanyRequestFlowTest extends UnitTestCase
{
    public function testAreStepsInProperOrder(): void
    {
        $createCompanyRequestFlow = new CreateCompanyRequestFlow();

        self::assertSame(CompanyCreateRequestBasicsType::class, $createCompanyRequestFlow->getStep(1)->getFormType());
        self::assertSame(CompanyCreateRequestAddressType::class, $createCompanyRequestFlow->getStep(2)->getFormType());
        self::assertSame(CompanyCreateRequestContactType::class, $createCompanyRequestFlow->getStep(3)->getFormType());
        self::assertSame(CompanyCreateRequestPaymentsType::class, $createCompanyRequestFlow->getStep(4)->getFormType());
        self::assertSame(CompanyCreateRequestSettingsType::class, $createCompanyRequestFlow->getStep(5)->getFormType());
    }
}
