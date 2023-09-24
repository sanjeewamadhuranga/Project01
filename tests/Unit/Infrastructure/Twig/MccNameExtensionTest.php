<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Twig;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Twig\MccNameExtension;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MccNameExtensionTest extends UnitTestCase
{
    private readonly SystemSettings&MockObject $systemSettings;

    private readonly MccNameExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->systemSettings = $this->createMock(SystemSettings::class);

        $this->systemSettings->method('getValue')->with(SystemSettings::MCC_LIST)->willReturn([
            'Real estate or construction' => [
                '5039' => 'Construction',
                '5074' => 'Plumbing and heating',
            ],
            'Charity or not-for-profit' => [
                '8398' => 'All activities',
            ],
        ]);

        $this->extension = new MccNameExtension($this->systemSettings);
    }

    public function testItRegistersFunctions(): void
    {
        $functions = $this->extension->getFunctions();
        self::assertCount(1, $functions);
        self::assertSame('get_mcc_name', $functions[0]->getName());
    }

    public function testItReturnsMccNameFromSettings(): void
    {
        self::assertSame('Construction', $this->extension->getMccName('5039'));
        self::assertSame('Plumbing and heating', $this->extension->getMccName('5074'));
        self::assertSame('All activities', $this->extension->getMccName('8398'));
    }

    public function testItFallbacksToHardcodedDictionary(): void
    {
        self::assertSame('Fax services, Telecommunication Services', $this->extension->getMccName('4814'));
    }

    public function testItReturnsNullIfMccNotFound(): void
    {
        self::assertNull($this->extension->getMccName('4810'));
    }
}
