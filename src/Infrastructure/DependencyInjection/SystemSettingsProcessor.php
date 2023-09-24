<?php

declare(strict_types=1);

namespace App\Infrastructure\DependencyInjection;

use App\Domain\Settings\SystemSettings;
use Closure;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class SystemSettingsProcessor implements EnvVarProcessorInterface
{
    public function __construct(private readonly SystemSettings $settings)
    {
    }

    public function getEnv(string $prefix, string $name, Closure $getEnv): mixed
    {
        return $this->settings->getValue($name);
    }

    public static function getProvidedTypes(): array
    {
        return [
            'setting' => 'bool|int|float|string|array',
        ];
    }
}
