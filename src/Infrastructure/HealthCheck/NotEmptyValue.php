<?php

declare(strict_types=1);

namespace App\Infrastructure\HealthCheck;

use Laminas\Diagnostics\Check\AbstractCheck;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

class NotEmptyValue extends AbstractCheck
{
    /**
     * @param array<string, string> $values
     */
    public function __construct(private readonly array $values, string $label)
    {
        $this->setLabel($label);
    }

    public function check(): ResultInterface
    {
        foreach ($this->values as $key => $value) {
            if ('' === $value) {
                return new Failure(sprintf('Missing %s', $key));
            }
        }

        return new Success('Already set necessary value(s).');
    }
}
