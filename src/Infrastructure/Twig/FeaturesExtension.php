<?php

declare(strict_types=1);

namespace App\Infrastructure\Twig;

use App\Domain\Settings\Features;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FeaturesExtension extends AbstractExtension
{
    public function __construct(private readonly Features $features)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_feature', $this->features->isFeatureEnabled(...)),
            new TwigFunction('kyc_enabled', $this->features->isKycEnabled(...)),
        ];
    }
}
