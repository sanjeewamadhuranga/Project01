<?php

declare(strict_types=1);

namespace App\Infrastructure\Menu;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Domain\Settings\FeatureInterface;

class ConfigurationMenu extends AbstractMenu
{
    /**
     * Main sections and subsections of the "Settings" page.
     */
    public function getItems(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_SETTINGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.merchant_settings.title',
                icon: 'fas fa-square merchant-settings-icon',
            )->setChildren([
                 MenuItem::create(
                     label: 'menu.merchant_settings.administration',
                 )->setChildren($this->getMerchantAdministrationSettings()),
                 MenuItem::create(
                     label: 'menu.merchant_settings.app',
                 )->setChildren($this->getMerchantAppSettings()),
                 MenuItem::create(
                     label: 'menu.merchant_settings.shop',
                 )->setChildren($this->getMerchantShopSettings()),
            ]);
        }

        if ($this->isGranted(Permission::CONFIGURATION_SETTINGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.system_wide_settings.title',
                icon: 'fas fa-circle system-settings-icon',
            )->setChildren([
                 MenuItem::create(
                     label: 'menu.system_wide_settings.platform',
                 )->setChildren($this->getSystemPlatformSettings()),
                 MenuItem::create(
                     label: 'menu.system_wide_settings.billing_and_plans',
                 )->setChildren($this->getSystemBillingAndPlansSettings()),
                 MenuItem::create(
                     label: 'menu.system_wide_settings.automation',
                 )->setChildren($this->getSystemAutomationSettings()),
                 MenuItem::create(
                     label: 'menu.system_wide_settings.advanced_settings',
                 )->setChildren($this->getSystemAdvancedSettings()),
                 MenuItem::create(
                     label: 'menu.system_wide_settings.payout_settings',
                 )->setChildren($this->getSystemPayoutSettings()),
            ]);
        }

        if ($this->isGranted(Permission::CONFIGURATION_SETTINGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu._portal_settings.title',
                icon: 'fas fa-square fa-rotate-by -settings-icon',
            )->setChildren([
                 MenuItem::create(
                     label: 'menu._portal_settings.status',
                 )->setChildren($this->getPortalStatusSettings()),
                 MenuItem::create(
                     label: 'menu._portal_settings.roles',
                 )->setChildren($this->getPortalRolesSettings()),
            ]);
        }
    }

    /**
     * Settings links for the "Merchant settings" - "Administration" section.
     *
     * @return iterable<MenuItem>
     */
    private function getMerchantAdministrationSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_BANK.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.merchant_roles',
                route: 'configuration_roles_index',
            );
        }

        if (
            $this->isGranted(Permission::CONFIGURATION_TERMS.Action::ANY) &&
            $this->config->getFeatures()->isTermsFeaturesEnabled()
        ) {
            yield MenuItem::create(
                label: 'menu.terms',
                route: 'configuration_terms_index',
            );
        }
    }

    /**
     * Settings links for the "Merchant settings" - "App" section.
     *
     * @return iterable<MenuItem>
     */
    private function getMerchantAppSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_API_STATUS)) {
            yield MenuItem::create(
                label: 'menu.status',
                route: 'configuration_status_index',
            );
        }

        if (
            $this->isGranted(Permission::CONFIGURATION_BETA_FEATURE.Action::ANY) &&
            $this->config->getFeatures()->isBetaFeaturesEnabled()
        ) {
            yield MenuItem::create(
                label: 'menu.betaFeatures',
                route: 'configuration_betafeature_index',
            );
        }
    }

    /**
     * Settings links for the "Merchant settings" - "Shop" section.
     *
     * @return iterable<MenuItem>
     */
    private function getMerchantShopSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_DISCOUNT_CODE.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.discount_codes',
                route: 'configuration_discount_code_index',
            );
        }
    }

    /**
     * Settings links for the "System wide settings" - "Platform" section.
     *
     * @return iterable<MenuItem>
     */
    private function getSystemPlatformSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_SETTINGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.configurations',
                route: 'configuration_settings_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_PROVIDERS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.providers',
                route: 'configuration_providers_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_COUNTRY.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.countries',
                route: 'configuration_country_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_BANK.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.bank',
                route: 'configuration_bank_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_APPS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.apps',
                route: 'configuration_apps_index',
            );
        }
    }

    /**
     * Settings links for the "System wide settings" - "Billing & Plans" section.
     *
     * @return iterable<MenuItem>
     */
    private function getSystemBillingAndPlansSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_MDR_BILLING.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.mdr_billing',
                route: 'configuration_mdr_billing_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_SUBSCRIPTION_PLAN.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.subscription_plans',
                route: 'configuration_subscription_plan_index',
            );
        }
    }

    /**
     * Settings links for the "System wide settings" - "Automation" section.
     *
     * @return iterable<MenuItem>
     */
    private function getSystemAutomationSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_RULES.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.rules',
                route: 'configuration_rules_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_INTEGRATION.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.integrations',
                route: 'configuration_integration_index',
            );
        }
    }

    /**
     * Settings links for the "System wide settings" - "Advanced settings" section.
     *
     * @return iterable<MenuItem>
     */
    private function getSystemAdvancedSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_SETTINGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.manage_branding',
                route: 'configuration_manage_branding',
            );

            yield MenuItem::create(
                label: 'menu.manage_localization',
                route: 'configuration_manage_localization',
            );

            yield MenuItem::create(
                label: 'menu.manage_features',
                route: 'configuration_manage_features',
            );

            yield MenuItem::create(
                label: 'menu.manage_tokenization',
                route: 'configuration_manage_tokenization',
            );

            yield MenuItem::create(
                label: 'menu.platform_setup_wizard',
                route: 'setup_branding',
            );
        }
    }

    /**
     * Settings links for the "System wide settings" - "Payout settings" section.
     *
     * @return iterable<MenuItem>
     */
    private function getSystemPayoutSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_SETTINGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.holiday_calendar',
                route: 'configuration_holiday_calendar_index',
            );
        }
        if ($this->isGranted(Permission::CONFIGURATION_SETTINGS.Action::ANY) && $this->config->getFeatures()->isFeatureEnabled(FeatureInterface::PAYOUT_OFFSET)) {
            yield MenuItem::create(
                label: 'menu.manage_payout_offset',
                route: 'configuration_manage_payout_offset',
            );
        }
    }

    /**
     * Settings links for the " settings" - "Status" section.
     *
     * @return iterable<MenuItem>
     */
    private function getPortalStatusSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_SETTINGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.status_check',
                route: 'configuration_status_check_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_LOGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.merchant_logs',
                route: 'configuration_merchant_log_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_MIGRATIONS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.migrations',
                route: 'configuration_migrations_index',
            );
        }

        if ($this->isGranted(Permission::CONFIGURATION_LOGS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.audit',
                route: 'configuration_log_index',
            );
        }
    }

    /**
     * Settings links for the " settings" - "Roles" section.
     *
     * @return iterable<MenuItem>
     */
    private function getPortalRolesSettings(): iterable
    {
        if ($this->isGranted(Permission::CONFIGURATION_ROLES.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.administrator_roles',
                route: 'configuration_manager_roles_index',
            );
        }
    }
}
