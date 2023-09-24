<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\HealthCheck;

use App\Infrastructure\HealthCheck\NotEmptyValue;
use App\Tests\Unit\UnitTestCase;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;

class NotEmptyValueTest extends UnitTestCase
{
    private const LABEL = 'Sample label';

    public function testItWillReturnFailureWhenProvideEmptyValue(): void
    {
        $notEmptyValue = new NotEmptyValue(['cloud_name' => ''], self::LABEL);
        $check = $notEmptyValue->check();
        self::assertInstanceOf(Failure::class, $check);
        self::assertSame('Sample label', $notEmptyValue->getLabel());
        self::assertSame('Missing cloud_name', $check->getMessage());
    }

    public function testItWillReturnSuccessWhenProvideCorrectData(): void
    {
        $notEmptyValue = new NotEmptyValue(['cloud_name' => 'test'], self::LABEL);
        self::assertInstanceOf(Success::class, $notEmptyValue->check());
        self::assertSame(self::LABEL, $notEmptyValue->getLabel());
    }
}
