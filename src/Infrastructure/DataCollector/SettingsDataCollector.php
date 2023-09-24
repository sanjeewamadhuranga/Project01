<?php

declare(strict_types=1);

namespace App\Infrastructure\DataCollector;

use App\Domain\Document\Setting;
use App\Domain\Settings\Config;
use Symfony\Bundle\FrameworkBundle\DataCollector\TemplateAwareDataCollectorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Throwable;

#[When('dev')]
#[AutoconfigureTag('data_collector', ['id' => 'settings'])]
class SettingsDataCollector extends DataCollector implements TemplateAwareDataCollectorInterface, LateDataCollectorInterface
{
    public function __construct(private readonly Config $config)
    {
    }

    public function lateCollect(): void
    {
        $this->data = [
            'settings' => array_map(
                static fn (Setting $setting): mixed => $setting->getValue(),
                $this->config->getSettings()->getAll()
            ),
            'features' => [
                'enabled' => $this->config->getFeatures()->getEnabledFeatures(),
                'all' => $this->config->getFeatures()::getConstants(),
            ],
            'branding' => [
                'owner' => $this->config->getBranding()->getOwner(),
                'theme' => $this->config->getBranding()->getTheme(),
            ],
        ];
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'settings';
    }

    public static function getTemplate(): ?string
    {
        return 'data_collector/settings.html.twig';
    }

    public function collect(Request $request, Response $response, Throwable $exception = null): void
    {
        // @see lateCollect()
    }

    public function getOwner(): string
    {
        return $this->data['branding']['owner'] ?? '';
    }

    public function getTheme(): string
    {
        return $this->data['branding']['theme'] ?? '';
    }

    /**
     * @return array{enabled: string[], all: array<string, string>}
     */
    public function getFeatures(): array
    {
        return $this->data['features'];
    }

    /**
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return $this->data['settings'];
    }
}
