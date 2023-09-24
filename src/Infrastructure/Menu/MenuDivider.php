<?php

declare(strict_types=1);

namespace App\Infrastructure\Menu;

use Closure;

class MenuDivider extends MenuItem
{
    /**
     * {@inheritDoc}
     */
    public static function create(
        ?string $label = null,
        ?string $route = null,
        array $routeParams = [],
        ?string $routeClass = null,
        ?Closure $routeClassCallback = null,
        ?string $icon = null,
        bool $isDivider = false
    ): MenuItem {
        return new MenuItem(
            label: $label,
            route: $route,
            routeParams: $routeParams,
            routeClass: $routeClass,
            routeClassCallback: $routeClassCallback,
            icon: $icon,
            isDivider: true
        );
    }
}
