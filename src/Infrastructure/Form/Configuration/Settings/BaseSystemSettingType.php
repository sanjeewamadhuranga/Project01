<?php

declare(strict_types=1);

namespace App\Infrastructure\Form\Configuration\Settings;

use App\Domain\Settings\Features;
use App\Domain\Settings\SystemSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseSystemSettingType extends AbstractType
{
    public function __construct(private readonly SystemSettings $systemSettings, private readonly RequestStack $requestStack)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data' => $this->getData(),
            'label_format' => 'configuration.settings.label.%name%',
            'translation_domain' => 'settings',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getData(): array
    {
        $data = $this->getDefaultData();
        foreach ($this->systemSettings->getAll() as $setting) {
            if (null !== $setting->getValue()) {
                $data[$setting->getName()] = $setting->getValue();
            }
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function getDefaultData(): array
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $domain = $request->server->get('REMOTE_ADDR');
        $domain = str_replace(['.', 'manager.'], '', $domain);

        return [
            SystemSettings::DASHBOARD => 'https://dashboard.'.$domain,
            SystemSettings::API_DOMAIN => 'https://api.'.$domain,
            SystemSettings::MANAGER_PORTAL_URL => 'https://.'.$domain,
            SystemSettings::ENABLED_TIMEZONES => [
                'default' => 'Europe/London',
                'Europe/London' => 'Europe/London',
                'Asia/Kolkata' => 'Asia/Kolkata',
                'Asia/Ho_Chi_Minh' => 'Asia/Ho_Chi_Minh',
                'Asia/Singapore' => 'Asia/Singapore',
            ],
            SystemSettings::ENABLED_CURRENCIES => [
                'default' => 'GBP',
                'GBP' => 'GBP',
                'EUR' => 'EUR',
                'USD' => 'USD',
                'SGD' => 'SGD',
                'AED' => 'AED',
                'HKD' => 'HKD',
                'CHF' => 'CHF',
                'AUD' => 'AUD',
            ],
            SystemSettings::ENABLED_COUNTRIES => [
                'default' => 'GB',
                'GB' => 'United Kingdom',
                'BE' => 'Belgium',
                'FR' => 'France',
                'NL' => 'The Netherlands',
                'VN' => 'Vietnam',
                'SG' => 'Singapore',
                'LK' => 'Sri Lanka',
            ],
            SystemSettings::ENABLED_LANGUAGES => [
                'default' => 'EN',
                'EN' => 'English',
                'VI' => 'Vietnamese',
            ],
            SystemSettings::ENABLED_FEATURES => array_diff(array_values(Features::getConstants()), [
                Features::LOGIN_FORCE_2FA,
            ]),
            SystemSettings::SHOW_REGISTRATION_PROVISION => 'true',
            SystemSettings::SHOW_OFFERS => 'true',
            SystemSettings::SHOW_COMPLIANCE => 'true',
            SystemSettings::SHOW_AUTOCREDITS => 'true',
            SystemSettings::PAYOUT_REPORT => 'true',
            SystemSettings::PLATFORM_BILLING => 'true',
            SystemSettings::SHOW_FX_SETTLEMENT_LIST => 'true',
            SystemSettings::SHOW_EDC_IMPORT => 'true',
            SystemSettings::SHOW_PROVISION_MERCHANT => 'true',
        ];
    }
}
