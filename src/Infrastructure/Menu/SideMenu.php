<?php

declare(strict_types=1);

namespace App\Infrastructure\Menu;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use Symfony\Component\HttpFoundation\Request;

class SideMenu extends AbstractMenu
{
    public function getItems(): iterable
    {
        yield MenuItem::create(
            label: 'menu.dashboard',
            route: 'dashboard',
            routeClass: 'dashboard',
            icon: 'home'
        );

        if ($this->isGranted(Permission::MODULE_MERCHANT.action::ANY)) {
            yield $this->getMerchantsMenu();
        }

        if ($this->isGranted(Permission::MODULE_MERCHANT_REQUEST.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.merchant_requests',
                route: 'merchant_requests_index',
                routeClass: 'merchant_requests_',
                icon: 'list-check'
            );
        }

        if ($this->config->getSettings()->showRegistrationProvision() && $this->isGranted(Permission::MODULE_ONBOARDING.Action::ANY)) {
            yield $this->getOnboardingMenu();
        }

        if ($this->isGranted(Permission::MODULE_TRANSACTION.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.transactions',
                route: 'transaction_index',
                routeClass: 'transaction_',
                icon: 'random'
            );
        }

        if ($this->isGranted(Permission::MODULE_REMITTANCE.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.remittance',
                route: 'remittance_index',
                routeClass: 'remittance_',
                icon: 'arrow-circle-down'
            );
        }

        if ($this->isGranted(Permission::MODULE_REPORTS.Action::ANY)) {
            yield $this->getReportsMenu();
        }

        if ($this->config->getFeatures()->hasCurrencyFXConfiguration() && $this->isGranted(Permission::MODULE_CURRENCY_FX.Action::ANY)) {
            yield $this->getFxMenu();
        }

        if ($this->config->getFeatures()->hasFeatureShops() && $this->isGranted(Permission::MODULE_MARKETPLACE.Action::ANY)) {
            yield $this->getMarketplaceMenu();
        }

        if ($this->config->getSettings()->showOffers() && $this->isGranted(Permission::MODULE_OFFER.Action::ANY)) {
            yield $this->getOffersMenu();
        }

        if ($this->config->getSettings()->showCompliance() && $this->isGranted(Permission::MODULE_COMPLIANCE.Action::ANY)) {
            yield $this->getComplianceMenu();
        }

        if (
            $this->isGranted(Permission::MODULE_CONFIGURATION.Action::ANY)
            || $this->isGranted(Permission::MODULE_ADMINISTRATORS.Action::ANY)
            || $this->isGranted(Permission::MODULE_COMPLIANCE.Action::ANY)
        ) {
            yield MenuItem::create(
                label: 'menu.config',
                isDivider: true
            );
        }

        if ($this->isGranted(Permission::MODULE_ADMINISTRATORS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.administrators',
                route: 'administrators_index',
                routeClass: 'administrators_',
                icon: 'user'
            );
        }

        if ($this->isGranted(Permission::MODULE_CONFIGURATION.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.settings',
                route: 'configuration_index',
                routeClass: 'configuration_',
                icon: 'cog'
            );
        }

        if ($this->isGranted(Permission::MODULE_NOTIFICATION.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.notifications_logs',
                routeClass: 'notifications_',
                icon: 'comment',
            )->setChildren($this->getNotificationsMenu());
        }
    }

    private function getMerchantsMenu(): MenuItem
    {
        return MenuItem::create(
            label: 'menu.merchants',
            route: 'merchants',
            routeClass: 'merchants_',
            icon: 'briefcase'
        )
            ->setChildren(
                [
                    MenuItem::create(
                        label: 'menu.merchants',
                        route: 'merchants_index',
                        routeClass: 'merchants_index'
                    ),
                    MenuItem::create(
                        label: 'menu.circles',
                        route: 'circles_index',
                        routeClass: 'circles_'
                    ),
                ]
            )
        ;
    }

    private function getOnboardingMenu(): MenuItem
    {
        return MenuItem::create(
            label: 'menu.onboarding',
            route: 'onboarding',
            routeClass: 'onboarding_',
            icon: 'users'
        )->setChildren($this->getOnboardingMenuChildren());
    }

    /**
     * @return iterable<MenuItem>
     */
    private function getOnboardingMenuChildren(): iterable
    {
        if ($this->isGranted(Permission::ONBOARDING_REGISTRATIONS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.registration',
                route: 'onboarding_registration_index',
                routeClass: 'onboarding_registration_index'
            );
        }
        if ($this->isGranted(Permission::ONBOARDING_FLOWS.Action::ANY)) {
            yield MenuItem::create(
                label: 'menu.flows',
                route: 'onboarding_flows_index',
                routeClass: 'onboarding_flows_index'
            );
        }
        if ($this->isGranted(Permission::ONBOARDING_FEDERATED_IDENTITY.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.federated_identities',
                route: 'onboarding_federated_identity_index',
                routeClass: 'onboarding_federated_identity_index'
            );
        }
        if ($this->isGranted(Permission::ONBOARDING_TIPS_AND_TRICKS.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.tips_and_tricks',
                route: 'onboarding_tips_and_tricks_index',
                routeClass: 'onboarding_tips_and_tricks_index'
            );
        }
        if ($this->isGranted(Permission::ONBOARDING_BATCH_DYNAMIC_CODE.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.batch_dynamic_codes',
                route: 'onboarding_batch_dynamic_code_index',
                routeClass: 'onboarding_batch_dynamic_code_index'
            );
        }
        if ($this->isGranted(Permission::ONBOARDING_INVITATIONS.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.invitations',
                route: 'onboarding_invitation_index',
                routeClass: 'onboarding_invitation_index'
            );
        }
    }

    private function getReportsMenu(): MenuItem
    {
        return MenuItem::create(
            label: 'menu.reports',
            route: 'reports',
            routeClass: 'reports_',
            icon: 'chart-bar'
        )->setChildren($this->getReportsMenuChildren());
    }

    /**
     * @return iterable<MenuItem>
     */
    private function getReportsMenuChildren(): iterable
    {
        if ($this->isGranted(Permission::REPORTS_TRANSACTION.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.transactions_reports',
                route: 'reports_transaction_index',
                routeClass: 'reports_transaction_'
            );
        }
        if ($this->config->getSettings()->showAutocredits()) {
            if ($this->isGranted(Permission::REPORTS_AUTOCREDIT.action::ANY)) {
                yield MenuItem::create(
                    label: 'menu.autoCredit',
                    route: 'reports_autocredit_index',
                    routeClass: 'reports_autocredit_'
                );
            }
        }
        if ($this->config->getSettings()->showPayoutReports()) {
            if ($this->isGranted(Permission::REPORTS_PAYOUT.action::ANY)) {
                yield MenuItem::create(
                    label: 'menu.payout_report',
                    route: 'reports_payout_index',
                    routeClass: 'reports_payout_'
                );
            }
        }
        if ($this->config->getSettings()->showPlatformBilling()) {
            if ($this->isGranted(Permission::REPORTS_PLATFORM_BILLING.action::ANY)) {
                yield MenuItem::create(
                    label: 'menu.billing_report',
                    route: 'reports_platform_billing_index',
                    routeClass: 'reports_platform_billing_'
                );
            }
        }
        if ($this->isGranted(Permission::REPORTS_MY.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.myReports',
                route: 'reports_my_index',
                routeClass: 'reports_my_'
            );
        }
    }

    private function getFxMenu(): MenuItem
    {
        return MenuItem::create(
            label: 'menu.currency_fx',
            route: 'currency_fx',
            routeClass: 'currency_fx_',
            icon: 'globe'
        )->setChildren($this->getFxMenuChildren());
    }

    /**
     * @return iterable<MenuItem>
     */
    private function getFxMenuChildren(): iterable
    {
        if ($this->isGranted(Permission::CURRENCY_FX_RATES.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.rates',
                route: 'currency_fx_rates_index',
                routeClass: 'currency_fx_rates_'
            );
        }
        if ($this->isGranted(Permission::CURRENCY_FX_ORDERS.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.entries',
                route: 'currency_fx_orders_index',
                routeClass: 'currency_fx_orders_'
            );
        }
        if ($this->isGranted(Permission::CURRENCY_FX_DCC_REBATE_REPORTS.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.dcc_rebate_reports',
                route: 'currency_fx_dcc_rebate_reports_index',
                routeClass: 'currency_fx_dcc_rebate_reports_'
            );
        }
        if ($this->config->getSettings()->showFxSettlementList()) {
            if ($this->isGranted(Permission::CURRENCY_FX_SETTLEMENT.action::ANY)) {
                yield MenuItem::create(
                    label: 'menu.settlements',
                    route: 'currency_fx_settlement_index',
                    routeClass: 'currency_fx_settlement_'
                );
            }
        }
    }

    private function getMarketplaceMenu(): MenuItem
    {
        return MenuItem::create(
            label: 'menu.marketplace',
            route: 'marketplace',
            routeClass: 'marketplace_',
            icon: 'cart-plus'
        )->setChildren($this->getMarketplaceMenuChildren());
    }

    private function getOffersMenu(): MenuItem
    {
        return MenuItem::create(
            label: 'menu.offers',
            route: 'offer',
            routeClass: 'offer_',
            icon: 'tag'
        )->setChildren($this->getOffersMenuChildren());
    }

    private function getComplianceMenu(): MenuItem
    {
        return MenuItem::create(
            label: 'menu.compliance',
            route: 'compliance',
            routeClass: 'compliance_',
            icon: 'shield-alt'
        )->setChildren($this->getComplianceMenuChildren());
    }

    /**
     * @return iterable<MenuItem>
     */
    private function getMarketplaceMenuChildren(): iterable
    {
        if ($this->isGranted(Permission::MARKETPLACE_PRODUCTS.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.products',
                route: 'marketplace_products_index',
                routeClass: 'marketplace_products_'
            );
        }
        if ($this->isGranted(Permission::MARKETPLACE_LOCATION.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.locations',
                route: 'marketplace_locations_index',
                routeClass: 'marketplace_locations_'
            );
        }
    }

    /**
     * @return iterable<MenuItem>
     */
    private function getOffersMenuChildren(): iterable
    {
        if ($this->isGranted(Permission::OFFER_DEAL.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.deals',
                route: 'offer_deals_index',
                routeClass: 'offer_deals_'
            );
        }
        if ($this->isGranted(Permission::OFFER_BENEFIT.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.benefit',
                route: 'offer_benefit_index',
                routeClass: 'offer_benefit_'
            );
        }
        if ($this->isGranted(Permission::OFFER_BRAND.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.brand',
                route: 'offer_brand_index',
                routeClass: 'offer_brand_'
            );
        }
        if ($this->isGranted(Permission::OFFER_CARDS.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.cards',
                route: 'offer_cards_index',
                routeClass: 'offer_cards_'
            );
        }
        if ($this->isGranted(Permission::OFFER_CATEGORY.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.category',
                route: 'offer_category_index',
                routeClass: 'offer_category_'
            );
        }
        if ($this->isGranted(Permission::OFFER_BLOCK.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.block',
                route: 'offer_block_index',
                routeClass: 'offer_block_'
            );
        }
    }

    /**
     * @return iterable<MenuItem>
     */
    private function getComplianceMenuChildren(): iterable
    {
        if ($this->isGranted(Permission::COMPLIANCE_CASE.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.case',
                route: 'compliance_case_index',
                routeClass: 'compliance_case_',
                routeClassCallback: fn (
                    string $routeClass,
                    Request $request
                ) => 'active' === $routeClass && '1' !== $request->get('onlyMe') ? 'active' : '',
            );
            yield MenuItem::create(
                label: 'menu.my_tasks',
                route: 'compliance_case_index',
                routeParams: ['onlyMe' => true],
                routeClass: 'compliance_case_',
                routeClassCallback: fn (
                    string $routeClass,
                    Request $request
                ) => 'active' === $routeClass && '1' === $request->get('onlyMe') ? 'active' : '',
            );
        }
        if ($this->isGranted(Permission::COMPLIANCE_DISPUTE.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.disputes',
                route: 'compliance_dispute_index',
                routeClass: 'compliance_dispute_'
            );
        }
        if ($this->isGranted(Permission::COMPLIANCE_RISK_PROFILE.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.risk_profiles',
                route: 'compliance_risk_profile_index',
                routeClass: 'compliance_risk_profile_'
            );
        }
    }

    /**
     * @return iterable<MenuItem>
     */
    private function getNotificationsMenu(): iterable
    {
        if ($this->isGranted(Permission::NOTIFICATION_EMAIL.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.notifications_email',
                route: 'notifications_index',
                routeParams: ['type' => 'email'],
                routeClass: 'notifications_index',
                routeClassCallback: fn (string $routeClass, Request $request) => 'email' === $request->attributes->get('type') ? 'active' : '',
            );
        }
        if ($this->isGranted(Permission::NOTIFICATION_SMS.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.notifications_sms',
                route: 'notifications_index',
                routeParams: ['type' => 'sms'],
                routeClass: 'notifications_index',
                routeClassCallback: fn (string $routeClass, Request $request) => 'sms' === $request->attributes->get('type') ? 'active' : '',
            );
        }
        if ($this->isGranted(Permission::NOTIFICATION_PUSH.action::ANY)) {
            yield MenuItem::create(
                label: 'menu.notifications_push',
                route: 'notifications_index',
                routeParams: ['type' => 'push'],
                routeClass: 'notifications_index',
                routeClassCallback: fn (string $routeClass, Request $request) => 'push' === $request->attributes->get('type') ? 'active' : '',
            );
        }
    }
}
