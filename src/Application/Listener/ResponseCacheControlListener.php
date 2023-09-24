<?php

declare(strict_types=1);

namespace App\Application\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * The primary responsibility of this listener is to set the cache control to "nocache".
 * As a result, when a user performs a logout action, it will prevent landing the previous page information by using the browser back button.
 *
 * @see https://www.tutorialspoint.com/http/http_caching.htm for all avaliable 'Cache-control' header attribute
 */
#[AsEventListener]
class ResponseCacheControlListener
{
    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $event->getResponse()->headers->add([
            'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
