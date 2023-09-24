<?php

declare(strict_types=1);

namespace App\Infrastructure\HealthCheck;

use Laminas\Diagnostics\Check\AbstractFileCheck;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

final class FileExists extends AbstractFileCheck
{
    protected function validateFile($file): ResultInterface
    {
        return new Success();
    }
}
