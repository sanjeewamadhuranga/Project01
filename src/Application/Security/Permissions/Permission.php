<?php

declare(strict_types=1);

namespace App\Application\Security\Permissions;

use App\Domain\Settings\ExposesConsts;

final class Permission
{
    use ExposesConsts;

    final public const MODULE_OFFER = 'offer.';
    final public const OFFER_BENEFIT = self::MODULE_OFFER.'benefit.';
    final public const OFFER_BLOCK = self::MODULE_OFFER.'block.';
    final public const OFFER_BRAND = self::MODULE_OFFER.'brand.';
    final public const OFFER_CARDS = self::MODULE_OFFER.'cards.';
    final public const OFFER_CATEGORY = self::MODULE_OFFER.'category.';
    final public const OFFER_DEAL = self::MODULE_OFFER.'deal.';

    final public const MODULE_CONFIGURATION = 'configuration.';
    final public const CONFIGURATION_API_STATUS = self::MODULE_CONFIGURATION.'api_status.';
    final public const CONFIGURATION_APPS = self::MODULE_CONFIGURATION.'apps.';
    final public const CONFIGURATION_BETA_FEATURE = self::MODULE_CONFIGURATION.'beta_feature.';
    final public const CONFIGURATION_COUNTRY = self::MODULE_CONFIGURATION.'country.';
    final public const CONFIGURATION_DISCOUNT_CODE = self::MODULE_CONFIGURATION.'discount_code.';
    final public const CONFIGURATION_INTEGRATION = self::MODULE_CONFIGURATION.'integration.';
    final public const CONFIGURATION_MDR_BILLING = self::MODULE_CONFIGURATION.'mdr_billing.';
    final public const CONFIGURATION_PROVIDERS = self::MODULE_CONFIGURATION.'providers.';
    final public const CONFIGURATION_ROLES = self::MODULE_CONFIGURATION.'roles.';
    final public const CONFIGURATION_RULES = self::MODULE_CONFIGURATION.'rules.';
    final public const CONFIGURATION_SETTINGS = self::MODULE_CONFIGURATION.'settings.';
    final public const CONFIGURATION_SUBSCRIPTION_PLAN = self::MODULE_CONFIGURATION.'subscription_plan.';
    final public const CONFIGURATION_LOGS = self::MODULE_CONFIGURATION.'logs.';
    final public const CONFIGURATION_MANAGER_ROLES = self::MODULE_CONFIGURATION.'manager_roles.';
    final public const CONFIGURATION_BANK = self::MODULE_CONFIGURATION.'bank.';
    final public const CONFIGURATION_MIGRATIONS = self::MODULE_CONFIGURATION.'migrations.';
    final public const CONFIGURATION_TERMS = self::MODULE_CONFIGURATION.'terms.';

    final public const MODULE_CURRENCY_FX = 'currency_fx.';
    final public const CURRENCY_FX_RATES = self::MODULE_CURRENCY_FX.'rates.';
    final public const CURRENCY_FX_DCC_REBATE_REPORTS = self::MODULE_CURRENCY_FX.'dcc_rebate_reports.';
    final public const CURRENCY_FX_ORDERS = self::MODULE_CURRENCY_FX.'orders.';
    final public const CURRENCY_FX_SETTLEMENT = self::MODULE_CURRENCY_FX.'settlement.';

    final public const MODULE_MARKETPLACE = 'marketplace.';
    final public const MARKETPLACE_LOCATION = self::MODULE_MARKETPLACE.'location.';
    final public const MARKETPLACE_PRODUCTS = self::MODULE_MARKETPLACE.'products.';

    final public const MODULE_ONBOARDING = 'onboarding.';
    final public const ONBOARDING_DYNAMIC_CODE = self::MODULE_ONBOARDING.'dynamic_code.';
    final public const ONBOARDING_BATCH_DYNAMIC_CODE = self::MODULE_ONBOARDING.'batch_dynamic_code.';
    final public const ONBOARDING_FEDERATED_IDENTITY = self::MODULE_ONBOARDING.'federated_identity.';
    final public const ONBOARDING_TIPS_AND_TRICKS = self::MODULE_ONBOARDING.'tips_and_tricks.';
    final public const ONBOARDING_FLOWS = self::MODULE_ONBOARDING.'flows.';
    final public const ONBOARDING_INVITATIONS = self::MODULE_ONBOARDING.'invitations.';
    final public const ONBOARDING_REGISTRATIONS = self::MODULE_ONBOARDING.'registrations.';

    final public const MODULE_REPORTS = 'reports.';
    final public const REPORTS_AUTOCREDIT = self::MODULE_REPORTS.'autocredit.';
    final public const REPORTS_MY = self::MODULE_REPORTS.'my.';
    final public const REPORTS_PAYOUT = self::MODULE_REPORTS.'payout.';
    final public const REPORTS_PLATFORM_BILLING = self::MODULE_REPORTS.'platform_billing.';
    final public const REPORTS_TRANSACTION = self::MODULE_REPORTS.'transaction.';
    final public const REPORTS_TRANSACTIONS = self::MODULE_REPORTS.'transactions.';
    final public const REPORTS_BENEFIT = self::MODULE_REPORTS.'benefit.';
    final public const REPORTS_FEDERATED_IDENTITY = self::MODULE_REPORTS.'federated_identity.';
    final public const REPORTS_LOCATIONS = self::MODULE_REPORTS.'locations.';
    final public const REPORTS_REMITTANCE = self::MODULE_REPORTS.'remittance.';
    final public const REPORTS_MERCHANTS = self::MODULE_REPORTS.'merchants.';

    final public const MODULE_COMPLIANCE = 'compliance.';
    final public const COMPLIANCE_CASE = self::MODULE_COMPLIANCE.'case.';
    final public const COMPLIANCE_DISPUTE = self::MODULE_COMPLIANCE.'dispute.';
    final public const COMPLIANCE_RISK_PROFILE = self::MODULE_COMPLIANCE.'risk_profile.';

    final public const MODULE_ADMINISTRATORS = 'administrators.';

    final public const MODULE_MERCHANT = 'merchant.';
    final public const MERCHANT_CIRCLES = self::MODULE_MERCHANT.'circles.';
    final public const MERCHANT_NOTES = self::MODULE_MERCHANT.'notes.';
    final public const MERCHANT_EVENTS = self::MODULE_MERCHANT.'events.';
    final public const MERCHANT_OVERVIEW = self::MODULE_MERCHANT.'overview.';
    final public const MERCHANT_DETAILS = self::MODULE_MERCHANT.'details.';
    final public const MERCHANT_FINANCIAL = self::MODULE_MERCHANT.'financial.';
    final public const MERCHANT_DATA = self::MODULE_MERCHANT.'data.';
    final public const MERCHANT_PAYMENT_METHOD = self::MODULE_MERCHANT.'payment_method.';
    final public const MERCHANT_STRUCTURE = self::MODULE_MERCHANT.'structure.';
    final public const MERCHANT_USERS = self::MODULE_MERCHANT.'users.';
    final public const MERCHANT_SMS = self::MODULE_MERCHANT.'sms.';
    final public const MERCHANT_EMAIL = self::MODULE_MERCHANT.'email.';
    final public const MERCHANT_PUSH_NOTIFICATION = self::MODULE_MERCHANT.'push_notification.';
    final public const MERCHANT_COMPLIANCE = self::MODULE_MERCHANT.'compliance.';
    final public const MERCHANT_BANK_ACCOUNT = self::MODULE_MERCHANT.'bank_account.';
    final public const MERCHANT_BRANDING = self::MODULE_MERCHANT.'branding.';
    final public const MERCHANT_LOCATIONS = self::MODULE_MERCHANT.'locations.';
    final public const MERCHANT_TERMS = self::MODULE_MERCHANT.'terms.';
    final public const MERCHANT_TERMINALS = self::MODULE_MERCHANT.'terminals.';

    final public const MODULE_NOTIFICATION = 'notifications.';
    final public const NOTIFICATION_SMS = self::MODULE_NOTIFICATION.'sms.';
    final public const NOTIFICATION_EMAIL = self::MODULE_NOTIFICATION.'email.';
    final public const NOTIFICATION_PUSH = self::MODULE_NOTIFICATION.'push.';

    final public const MODULE_REMITTANCE = 'remittance.';
    final public const MODULE_TRANSACTION = 'transaction.';
    final public const TRANSACTION_MANUAL_FX = self::MODULE_TRANSACTION.'create_fx.';

    final public const MODULE_MERCHANT_REQUEST = 'merchant_request.';

    /**
     * List of permission prefixes to use custom actions (not just standard CRUD).
     *
     * @return array<string, string[]>
     */
    private static function getCustomActions(): array
    {
        return [
            self::MODULE_TRANSACTION => [
                Action::VIEW,
                Action::CREATE,
                Action::EDIT,
                Action::DOWNLOAD,
                Action::REQUEST_REFUND,
                Action::RETRY_SECONDARY_ACQUIRING,
            ],
            self::MODULE_ADMINISTRATORS => [
                ...Action::CRUD,
                Action::DOWNLOAD,
                Action::ENABLE,
                Action::DISABLE,
            ],
            self::MODULE_ONBOARDING => [Action::CREATE],
            self::MODULE_MERCHANT => Action::CRUD,
            self::MERCHANT_OVERVIEW => [Action::VIEW],
            self::MERCHANT_DATA => [Action::VIEW, Action::EDIT],
            self::MERCHANT_NOTES => [
                ...Action::CRUD,
                Action::COMPLETE,
            ],
            self::MERCHANT_EVENTS => [Action::VIEW],
            self::MERCHANT_COMPLIANCE => [Action::VIEW, Action::DOWNLOAD, Action::CREATE],
            self::MERCHANT_PAYMENT_METHOD => [Action::VIEW, Action::ENABLE, Action::DISABLE],
            self::MERCHANT_USERS => [Action::VIEW, Action::EDIT, Action::DELETE],
            self::MERCHANT_TERMS => [Action::VIEW, Action::CREATE],
            self::MERCHANT_BRANDING => [Action::VIEW, Action::EDIT],
            self::MERCHANT_DETAILS => [Action::VIEW, Action::EDIT],
            self::MERCHANT_PUSH_NOTIFICATION => [Action::VIEW, Action::CREATE],
            self::MERCHANT_SMS => [Action::VIEW, Action::CREATE],
            self::MERCHANT_EMAIL => [Action::VIEW, Action::CREATE],
            self::COMPLIANCE_CASE => [
                Action::REVIEW, Action::APPROVE,
                Action::ASSIGN, ...Action::CRUD, ],
            self::CONFIGURATION_APPS => [Action::VIEW],
            self::CURRENCY_FX_SETTLEMENT => [Action::EDIT, Action::VIEW],
            self::REPORTS_AUTOCREDIT => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_MY => [Action::VIEW, Action::DOWNLOAD, Action::DELETE],
            self::REPORTS_PAYOUT => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_TRANSACTIONS => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_TRANSACTION => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_MERCHANTS => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_FEDERATED_IDENTITY => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_BENEFIT => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_PLATFORM_BILLING => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_REMITTANCE => [Action::VIEW, Action::DOWNLOAD],
            self::REPORTS_LOCATIONS => [Action::DOWNLOAD],
            self::NOTIFICATION_SMS => [Action::VIEW],
            self::NOTIFICATION_EMAIL => [Action::VIEW],
            self::NOTIFICATION_PUSH => [Action::VIEW],
            self::MODULE_REMITTANCE => [Action::VIEW, Action::EDIT],
            self::ONBOARDING_BATCH_DYNAMIC_CODE => [...Action::CRUD, Action::DOWNLOAD],
            self::CONFIGURATION_MIGRATIONS => [Action::EDIT, Action::VIEW],
            self::CONFIGURATION_TERMS => [Action::CREATE, Action::VIEW],
            self::ONBOARDING_REGISTRATIONS => [Action::VIEW, Action::DELETE],
            self::ONBOARDING_INVITATIONS => [Action::CREATE, Action::VIEW, Action::DELETE],
            self::MODULE_MERCHANT_REQUEST => [Action::ASSIGN, Action::VIEW, Action::EDIT],
        ];
    }

    /**
     * Get all available permission combinations.
     * By default, it will assign {@see Action::CRUD} to each permission prefix and {@see Action::ANY} to each module.
     * This behaviour can be customized by modifying {@see getCustomActions}.
     *
     * Since this functionality relies on reflection, the results are cached internally for better performance.
     *
     * @return string[]
     */
    public static function getAllPermissions(): array
    {
        static $permissions;

        if (!isset($permissions)) {
            $customActions = self::getCustomActions();
            $permissions = [Action::ANY]; // Initialize with wildcard permission

            foreach (self::getConstants() as $key => $permission) {
                // Look up for CUSTOM_ACTIONS, default to a wildcard for modules or all actions for permissions
                $actions = $customActions[$permission] ?? (str_starts_with($key, 'MODULE_') ? [Action::ANY] : Action::CRUD);

                foreach ($actions as $action) {
                    $permissions[] = $permission.$action;
                }
            }

            sort($permissions);

            $permissions = array_values(array_unique($permissions));
        }

        return $permissions;
    }

    /**
     * @return string[]
     */
    public static function getAdministratorPermissions(): array
    {
        return array_merge([
            Action::ANY,
            self::MODULE_ADMINISTRATORS.Action::DISABLE,
            self::MODULE_ADMINISTRATORS.Action::DOWNLOAD,
            self::MODULE_ADMINISTRATORS.Action::ENABLE,
        ], array_map(fn (string $name) => self::MODULE_ADMINISTRATORS.$name, Action::CRUD));
    }
}
