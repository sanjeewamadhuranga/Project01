<?php

declare(strict_types=1);

namespace App\Application\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

abstract class RouteCheckingListener
{
    /**
     * @return string[]
     */
    protected function getAllowedRoutes(): array
    {
        return [
            'profile_change_password',
            'fos_js_routing_js',
        ];
    }

    public function isAllowedRoute(Request $request): bool
    {
        return in_array($request->attributes->get('_route'), $this->getAllowedRoutes(), true);
    }

    abstract public function __invoke(RequestEvent $event): void;
}
