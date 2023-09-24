<?php

declare(strict_types=1);

namespace App\Infrastructure\HealthCheck;

use App\Domain\Settings\Features;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class EnableFeatureCheck implements CheckInterface
{
    public function __construct(private readonly Features $features)
    {
    }

    public function check(): ResultInterface
    {
        $missingFeatures = array_diff(Features::getConstants(), $this->features->getEnabledFeatures());
        if (count($missingFeatures) > 0) {
            return new Warning(sprintf('%s and other %s features have not set.', array_values($missingFeatures)[0], count($missingFeatures) - 1));
        }

        return new Success('Check success');
    }

    public function getLabel(): string
    {
        return 'Enabled features check';
    }
}
