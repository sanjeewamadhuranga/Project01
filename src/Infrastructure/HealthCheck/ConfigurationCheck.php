<?php

declare(strict_types=1);

namespace App\Infrastructure\HealthCheck;

use App\Domain\Settings\SettingsInterface;
use App\Domain\Settings\SystemSettings;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class ConfigurationCheck implements CheckInterface
{
    public function __construct(private readonly SystemSettings $systemSettings)
    {
    }

    public function check(): ResultInterface
    {
        foreach ($this->getPrimarySettings() as $setting) {
            if (!$this->hasSetting($setting)) {
                return new Failure(sprintf('Missing %s', $setting));
            }
        }

        foreach ($this->getSecondarySettings() as $setting) {
            if (!$this->hasSetting($setting)) {
                return new Warning(sprintf('Missing %s', $setting));
            }
        }

        return new Success('All necessary configuration are set');
    }

    public function getLabel(): string
    {
        return 'Check configurations';
    }

    private function hasSetting(string $name): bool
    {
        return !in_array($this->systemSettings->getValue($name), [null, []], true);
    }

    /**
     * @return string[]
     */
    private function getPrimarySettings(): array
    {
        return [
            SettingsInterface::BASE_CURRENCY,
            SettingsInterface::ENABLED_TIMEZONES,
            SettingsInterface::SYSTEM_LOCALE,
            SettingsInterface::ADMIN_THEME,
            SettingsInterface::ENABLED_LANGUAGES,
            SettingsInterface::DEFAULT_TENANT_PAYMENT_PORTAL_NAME,
            SettingsInterface::DEFAULT_TERMS_AND_CONDITIONS,
            SettingsInterface::TENANT_TERMS_AND_CONDITIONS,
            SettingsInterface::TENANT_TERMS_TEXT,
        ];
    }

    /**
     * @return string[]
     */
    private function getSecondarySettings(): array
    {
        return [
            SettingsInterface::UNION_PAY_UAIS_SIGN_CERT_ID,
            SettingsInterface::UNION_PAY_UAIS_SIGN_PUBLIC_KEY,
            SettingsInterface::UNION_PAY_UAIS_ENC_CERT_ID,
            SettingsInterface::UNION_PAY_UAIS_ENC_PUBLIC_KEY,
            SettingsInterface::UNION_PAY_ACQUIRER_SIGN_CERT_ID,
            SettingsInterface::UNION_PAY_ACQUIRER_SIGN_PRIVATE_KEY,
            SettingsInterface::UNION_PAY_ACQUIRER_SIGN_PUBLIC_KEY,
            SettingsInterface::UNION_PAY_ACQUIRER_ENC_CERT_ID,
            SettingsInterface::UNION_PAY_ACQUIRER_ENC_PUBLIC_KEY,
            SettingsInterface::UNION_PAY_ACQUIRER_ENC_PRIVATE_KEY,
            SettingsInterface::UNION_PAY_ACQUIRER_ENC_CERT_ID_NEW,
            SettingsInterface::UNION_PAY_ACQUIRER_ENC_PUBLIC_KEY_NEW,
            SettingsInterface::UNION_PAY_ACQUIRER_ENC_PRIVATE_KEY_NEW,
            SettingsInterface::UNION_PAY_ACQUIRER_SIGN_CERT_ID_NEW,
            SettingsInterface::UNION_PAY_ACQUIRER_SIGN_PUBLIC_KEY_NEW,
            SettingsInterface::UNION_PAY_ACQUIRER_SIGN_PRIVATE_KEY_NEW,
            SettingsInterface::UNION_PAY_KEY_EXCHANGE,
        ];
    }
}
