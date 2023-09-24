<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoutingExtension extends AbstractExtension
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('route_class', $this->activeRouteClass(...)),
        ];
    }

    public function activeRouteClass(string $routePrefix, string $className = 'active', string $defaultClass = ''): string
    {
        $request = $this->requestStack->getMainRequest();

        if (null === $request) {
            return '';
        }

        $currentRoute = (string) $request->attributes->get('_route', '');

        return str_starts_with($currentRoute, $routePrefix) ? $className : $defaultClass;
    }
}
