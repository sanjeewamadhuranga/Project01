<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration\Setting;

use App\Domain\Settings\SystemSettings;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class BaseSystemSettingTest extends TypeTestCase
{
    protected SystemSettings&Stub $settings;

    protected Request&Stub $request;

    protected RequestStack&Stub $requestStack;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);

        $this->request = $this->createStub(Request::class);

        $this->requestStack = $this->createStub(RequestStack::class);
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);

        parent::setUp();
    }
}
