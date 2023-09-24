<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener;

use App\Application\Listener\TimezoneListener;
use App\Domain\Settings\SystemSettings;
use App\Tests\TouchesTimeZone;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TimezoneListenerTest extends UnitTestCase
{
    use TouchesTimeZone;

    private TimezoneListener $subscriber;

    private SystemSettings&MockObject $systemSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->systemSettings = $this->createMock(SystemSettings::class);
        $this->subscriber = new TimezoneListener($this->systemSettings);
    }

    public function testItChangesGlobalTimeZoneAccordingToSettings(): void
    {
        $this->systemSettings->expects(self::once())->method('getManagerTimezone')->willReturn('Europe/London');
        self::assertSame('UTC', date_default_timezone_get());
        $this->subscriber->__invoke($this->createStub(RequestEvent::class));
        self::assertSame('Europe/London', date_default_timezone_get());
    }

    public function testItDoesNothingIfTimeZoneIsInvalid(): void
    {
        $this->systemSettings->expects(self::once())->method('getManagerTimezone')->willReturn('TEST');
        self::assertSame('UTC', date_default_timezone_get());
        $this->subscriber->__invoke($this->createStub(RequestEvent::class));
        self::assertSame('UTC', date_default_timezone_get());
    }
}
