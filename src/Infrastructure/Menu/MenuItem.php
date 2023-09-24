<?php

declare(strict_types=1);

namespace App\Infrastructure\Menu;

use Closure;

class MenuItem
{
    /**
     * @var MenuItem[]
     */
    protected iterable $children = [];

    /**
     * @param array<string, mixed> $routeParams
     */
    protected function __construct(
        protected ?string $label = null,
        protected ?string $route = null,
        protected array $routeParams = [],
        protected ?string $routeClass = null,
        protected ?Closure $routeClassCallback = null,
        protected ?string $icon = null,
        protected bool $isDivider = false
    ) {
    }

    /**
     * @param array<string, mixed> $routeParams
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
            isDivider: $isDivider
        );
    }

    /**
     * @param iterable<MenuItem> $children
     */
    public function setChildren(iterable $children): MenuItem
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren(): iterable
    {
        return $this->children;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function getRouteClass(): ?string
    {
        return $this->routeClass;
    }

    public function getRouteClassCallback(): ?Closure
    {
        return $this->routeClassCallback;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function isDivider(): ?bool
    {
        return $this->isDivider;
    }
}
