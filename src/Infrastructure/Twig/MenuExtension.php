<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use App\Infrastructure\Menu\MenuInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /**
     * @param array<string, MenuInterface> $menus
     */
    public function __construct(private readonly array $menus)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu', $this->menu(...)),
        ];
    }

    public function menu(string $type): MenuInterface
    {
        return $this->menus[$type];
    }
}
