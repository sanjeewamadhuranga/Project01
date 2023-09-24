<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Settings\SystemSettings;
use DateTimeZone;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * The responsibility of this listener is to change the default timezone of the system according to system setting.
 */
#[AsEventListener(priority: 20)]
#[When('prod')]
#[When('dev')]
class TimezoneListener
{
    public function __construct(private readonly SystemSettings $systemSettings)
    {
    }

    public function __invoke(RequestEvent $events): void
    {
        $timezone = $this->systemSettings->getManagerTimezone();

        try {
            date_default_timezone_set((new DateTimeZone($timezone))->getName());
        } catch (Exception) {
            // do nth
        }
    }
}
