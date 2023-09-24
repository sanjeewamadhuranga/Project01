<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Twig;

use App\Infrastructure\Twig\QrCodeExtension;
use App\Tests\Unit\UnitTestCase;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;
use PHPUnit\Framework\MockObject\MockObject;

class QrCodeExtensionTest extends UnitTestCase
{
    private QrCodeExtension $extension;

    private BuilderInterface&MockObject $builder;

    protected function setUp(): void
    {
        $this->builder = $this->createMock(BuilderInterface::class);

        $this->extension = new QrCodeExtension($this->builder);
    }

    public function testItRegistersFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        self::assertCount(1, $functions);
        self::assertSame('qr_code', $functions[0]->getName());
    }

    public function testItGeneratesQrCode(): void
    {
        $resultString = uniqid('', true);

        $result = $this->createStub(ResultInterface::class);
        $result->method('getDataUri')->willReturn($resultString);

        $this->builder->method('data')->willReturn($this->builder);
        $this->builder->method('build')->willReturn($result);

        self::assertSame($resultString, $this->extension->qrCode('test'));
    }
}
