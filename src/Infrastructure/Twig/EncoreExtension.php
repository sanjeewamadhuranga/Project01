<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EncoreExtension extends AbstractExtension
{
    public function __construct(
        private readonly EntrypointLookupInterface $entrypointLookup,
        private readonly string $publicDir
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('encore_entry_css_source', $this->getEncoreEntryCssSource(...)),
        ];
    }

    public function getEncoreEntryCssSource(string $entryName): string
    {
        $source = '';
        foreach ($this->entrypointLookup->getCssFiles($entryName) as $file) {
            $source .= file_get_contents($this->publicDir.'/'.$file);
        }

        return $source;
    }
}
