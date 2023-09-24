<?php

declare(strict_types=1);

namespace App\Application\Security\Permissions;

use App\Domain\Settings\ExposesConsts;

final class OldPermission
{
    use ExposesConsts;

    // CompanyController
    final public const MOD_VIEW_MERCHANTS = 'admin_merchants';
    final public const MOD_VIEW_INDIVIDUAL_MERCHANT = 'admin_merchants_get';
    final public const MOD_MERCHANTS_EDIT_REVIEW_STATUS = 'admin_merchants_edit_review_status';
    final public const MOD_MERCHANTS_EDIT_BRANDING = 'admin_merchants_edit_branding';
    final public const MOD_MERCHANTS_EDIT_ADDRESS = 'admin_merchants_edit_address';
    final public const MOD_MERCHANTS_EDIT_PAYMENTS = 'admin_merchants_edit_payments';
    final public const MOD_MERCHANTS_EDIT_RESELLER_METADATA = 'admin_merchants_edit_reseller_metadata';
    final public const MOD_MERCHANTS_EDIT_STRUCTURE = 'admin_merchants_edit_structure';
    final public const MOD_MERCHANTS_EDIT_SUBSCRIPTION_PLAN = 'admin_merchants_edit_subscription_plan';
    final public const MOD_MERCHANTS_EDIT_BANK_ACCOUNTS = 'admin_merchants_edit_bank_accounts';
    final public const MOD_MERCHANTS_CONFIRM_DELETE = 'admin_merchants_delete_confirmation';
    final public const MOD_MERCHANTS_REPORTS_EXPORT = 'admin_reports_merchants_export';
    final public const MOD_MERCHANTS_DELETE = 'admin_merchants_delete';
    final public const MOD_MERCHANTS_ADD_COMPLIANCE_DOCUMENT = 'admin_merchants_compliance_add_document';
    final public const MOD_MERCHANTS_COMPLIANCE_DOWNLOAD_DOCUMENT = 'admin_merchants_compliance_download_document';
    final public const MOD_ADMIN_MERCHANTS_SEND_EMAIL = 'admin_merchants_send_email';
    final public const MOD_ADMIN_MERCHANTS = 'admin_merchants_lists';
    final public const MOD_ADMIN_MERCHANTS_LISTS = 'admin_merchants_lists';
    final public const MOD_ADMIN_MERCHANTS_TERMS_CREATE = 'admin_merchant_terms_create';

    // BetaFeatureController
    final public const MOD_ADDONS_BETAFEATUES = 'admin_addons_betafeatures';
    final public const MOD_ADDONS_BETAFEATURES_CREATE = 'admin_addons_betafeatures_create';
    final public const MOD_ADDONS_BETAFEATURES_EDIT = 'admin_addons_betafeatures_edit';
    final public const MOD_ADDONS_BETAFEATURES_GET = 'admin_addons_betafeatures_get';
    final public const MOD_ADDONS_BETAFEATURES_DELETE = 'admin_addons_betafeatures_delete';

    // AccountManagerNoteController
    final public const MOD_ADMIN_MERCHANT_NOTE_CREATE = 'app_company_accountmanagernote_create';
    final public const MOD_ADMIN_MERCHANT_NOTE_TOGGLE = 'app_company_accountmanagernote_toggle';

    // PayoutBlockController
    final public const MOD_ADMIN_COMPLIANCE_LIST = 'admin_compliance_list';
    final public const MOD_ADMIN_COMPLIANCE_MY_WORKFLOW = 'admin_compliance_my_workflow';
    final public const MOD_ADMIN_COMPLIANCE_CREATE = 'admin_compliance_create';
    final public const MOD_ADMIN_COMPLIANCE_EDIT = 'admin_compliance_edit';
    final public const MOD_ADMIN_COMPLIANCE_ASSIGN_HANDLER = 'admin_compliance_assign_handler';
    final public const MOD_ADMIN_COMPLIANCE_ASSIGN_APPROVER = 'admin_compliance_assign_approver';
    final public const MOD_ADMIN_COMPLIANCE_DELETE = 'admin_compliance_delete';
    final public const MOD_ADMIN_COMPLIANCE_ADMIN = 'admin_compliance';

    // RiskProfileController
    final public const MOD_ADMIN_COMPLIANCE_PROFILES = 'admin_compliance_profiles';
    final public const MOD_ADMIN_COMPLIANCE_PROFILES_GET = 'admin_compliance_profiles_get';
    final public const MOD_ADMIN_COMPLIANCE_PROFILES_EDIT = 'admin_compliance_profiles_edit';
    final public const MOD_ADMIN_COMPLIANCE_PROFILES_CREATE = 'admin_compliance_profiles_create';
    final public const MOD_ADMIN_COMPLIANCE_PROFILES_DELETE = 'admin_compliance_profiles_delete';

    // PayoutBlockController
    final public const MOD_ADMIN_CONFIG_INTEGRATION = 'admin_configuration_integrations';
    final public const MOD_ADMIN_CONFIG_INTEGRATION_GET = 'admin_configuration_integrations_get';
    final public const MOD_ADMIN_CONFIG_INTEGRATION_EDIT = 'admin_configuration_integrations_edit';
    final public const MOD_ADMIN_CONFIG_INTEGRATION_CREATE = 'admin_configuration_integrations_create';
    final public const MOD_ADMIN_CONFIG_INTEGRATION_DELETE = 'admin_configuration_integrations_delete';

    // MdrBillingController
    final public const MOD_ADMIN_CONFIG_MDR_BILLING = 'admin_configuration_mdr_billing';
    final public const MOD_ADMIN_CONFIG_MDR_BILLING_GET = 'admin_configuration_mdr_billing_get';
    final public const MOD_ADMIN_CONFIG_MDR_BILLING_EDIT = 'admin_configuration_mdr_billing_edit';
    final public const MOD_ADMIN_CONFIG_MDR_BILLING_CREATE = 'admin_configuration_mdr_billing_create';
    final public const MOD_ADMIN_CONFIG_MDR_BILLING_DELETE = 'admin_configuration_mdr_billing_delete';

    // DocRebateReportController
    final public const MOD_ADMIN_CURRENCYFX_DCC_REBATE_REPORTS = 'admin_currencyfx_dcc_rebate_reports';
    final public const MOD_ADMIN_CURRENCYFX_DCC_REBATE_REPORTS_GET = 'admin_currencyfx_dcc_rebate_reports_get';

    // SettlementController
    final public const MOD_ADMIN_CURRENCYFX_SETTLEMENTS = 'admin_currencyfx_settlements';
    final public const MOD_ADMIN_CURRENCYFX_SETTLEMENTS_GET = 'admin_currencyfx_settlements_get';
    final public const MOD_ADMIN_CURRENCYFX_SETTLEMENTS_EDIT = 'admin_currencyfx_settlements_edit';

    // BenefitController
    final public const MOD_ADMIN_OFFERS_BENEFITS = 'admin_offers_benefits';
    final public const MOD_ADMIN_OFFERS_BENEFITS_GET = 'admin_offers_benefits_get';
    final public const MOD_ADMIN_OFFERS_BENEFITS_EDIT = 'admin_offers_benefits_edit';
    final public const MOD_ADMIN_OFFERS_BENEFITS_CREATE = 'admin_offers_benefits_create';
    final public const MOD_ADMIN_OFFERS_BENEFITS_DELETE = 'admin_offers_benefits_delete';
    final public const MOD_ADMIN_OFFERS_BENEFITS_TOGGLE = 'admin_offers_benefits_toggle';
    final public const MOD_ADMIN_LINKED_RESOURCE_ROUTE = 'admin_offers_benefits_delete_resource';

    // BlockController
    final public const MOD_ADMIN_OFFERS_BLOCKS = 'admin_offers_blocks';
    final public const MOD_ADMIN_OFFERS_BLOCKS_GET = 'admin_offers_blocks_get';
    final public const MOD_ADMIN_OFFERS_BLOCKS_EDIT = 'admin_offers_blocks_edit';
    final public const MOD_ADMIN_OFFERS_BLOCKS_CREATE = 'admin_offers_blocks_create';
    final public const MOD_ADMIN_OFFERS_BLOCKS_DELETE = 'admin_offers_blocks_delete';

    // BrandController
    final public const MOD_ADMIN_OFFERS_BRANDS = 'admin_offers_brands';
    final public const MOD_ADMIN_OFFERS_BRANDS_GET = 'admin_offers_brands_get';
    final public const MOD_ADMIN_OFFERS_BRANDS_EDIT = 'admin_offers_brands_edit';
    final public const MOD_ADMIN_OFFERS_BRANDS_CREATE = 'admin_offers_brands_create';
    final public const MOD_ADMIN_OFFERS_BRANDS_DELETE = 'admin_offers_brands_delete';

    // CardController
    final public const MOD_ADMIN_OFFERS_CARDS = 'admin_offers_cards';
    final public const MOD_ADMIN_OFFERS_CARDS_GET = 'admin_offers_cards_get';
    final public const MOD_ADMIN_OFFERS_CARDS_EDIT = 'admin_offers_cards_edit';
    final public const MOD_ADMIN_OFFERS_CARDS_CREATE = 'admin_offers_cards_create';
    final public const MOD_ADMIN_OFFERS_CARDS_DELETE = 'admin_offers_cards_delete';

    // CategoryController
    final public const MOD_ADMIN_OFFERS_CATEGORIES = 'admin_offers_categories';
    final public const MOD_ADMIN_OFFERS_CATEGORIES_GET = 'admin_offers_categories_get';
    final public const MOD_ADMIN_OFFERS_CATEGORIES_EDIT = 'admin_offers_categories_edit';
    final public const MOD_ADMIN_OFFERS_CATEGORIES_CREATE = 'admin_offers_categories_create';
    final public const MOD_ADMIN_OFFERS_CATEGORIES_DELETE = 'admin_offers_categories_delete';

    // DealController
    final public const MOD_ADMIN_OFFERS_DEALS = 'admin_offers_deals';
    final public const MOD_ADMIN_OFFERS_DEALS_GET = 'admin_offers_deals_get';
    final public const MOD_ADMIN_OFFERS_DEALS_EDIT = 'admin_offers_deals_edit';
    final public const MOD_ADMIN_OFFERS_DEALS_CREATE = 'admin_offers_deals_create';
    final public const MOD_ADMIN_OFFERS_DEALS_DELETE = 'admin_offers_deals_delete';
    final public const MOD_ADMIN_OFFERS_DEALS_TOGGLE = 'admin_offers_deals_toggle';
    final public const MOD_ADMIN_OFFERS_DEALS_DELETE_IMAGE = 'admin_offers_deals_delete_image';

    // PlatformBillingReportController
    final public const MOD_ADMIN_REPORTS_PLATFORMBILLING = 'admin_reports_platformbilling';
    final public const MOD_ADMIN_REPROTS_PLATFORMBILLING_GET = 'admin_reports_platformbilling_get';

    // TransactionReportController
    final public const MOD_ADMIN_TRANSACTIONS_REPORTS = 'admin_transactionreports';
    final public const MOD_ADMIN_TRANSACTIONS_REPORT_GET = 'admin_transactionreports_get';
    final public const MOD_ADMIN_TRANSACTIONS_REPORTS_DOWNLOAD = 'admin_transactionreports_download';

    // CognitoController
    final public const MOD_ADMIN_SYSTEM_COGNITO_LISTS = 'admin_system_cognito_list';

    // ApiStatusController
    final public const MOD_ADMIN_CONFIGURATION_STATUS = 'admin_configuration_status';
    final public const MOD_ADMIN_CONFIGURATION_STATUS_CREATE = 'admin_configuration_status_create';
    final public const MOD_ADMIN_CONFIGURATION_STATUS_EDIT = 'admin_configuration_status_edit';
    final public const MOD_ADMIN_CONFIGURATION_STATUS_DELETE = 'admin_configuration_status_delete';

    // AppsController
    final public const MOD_ADMIN_APPS = 'admin_apps';

    // AutoCreditController
    final public const MOD_AUTOCREDITS = 'admin_autocredits';
    final public const MOD_AUTOCREDITS_DOWNLOAD = 'admin_autocredits_download';

    // CardController
    final public const MOD_ADMIN_CARDS = 'admin_cards';

    // Configuration Controller
    final public const MOD_ADMIN_CONFIGURATION_PROVIDERS = 'admin_configuration_providers';
    final public const MOD_ADMIN_CONFIGURATION_PROVIDERS_CREATE = 'admin_configuration_providers_create';
    final public const MOD_ADMIN_CONFIGURATION_PROVIDERS_EDIT = 'admin_configuration_providers_edit';
    final public const MOD_ADMIN_CONFIGURATION_COUNTRIES = 'admin_configuration_countries';
    final public const MOD_ADMIN_CONFIGURATION_COUNTRIES_CREATE = 'admin_configuration_countries_create';
    final public const MOD_ADMIN_CONFIGURATION_COUNTRIES_EDIT = 'admin_configuration_countries_edit';
    final public const MOD_ADMIN_CONFIGURATION_SETTINGS = 'admin_configuration_settings';
    final public const MOD_ADMIN_CONFIGURATION_SETTINGS_CREATE = 'admin_configuration_settings_create';
    final public const MOD_ADMIN_CONFIGURATION_SETTINGS_DELETE = 'admin_configuration_settings_delete';
    final public const MOD_ADMIN_CONFIGURATION_SETTING_EDIT = 'admin_configuration_settings_edit';

    // Provision Controller
    final public const MOD_ADMIN_PROVISION_ACTION = 'admin_merchants_provision';

    // CurrencyFxController
    final public const MOD_ADMIN_CURRENCYFX = 'admin_currencyfx';
    final public const MOD_ADMIN_CURRENCYFX_GET = 'admin_currencyfx_get';
    final public const MOD_ADMIN_CURRENCYFX_EDIT = 'admin_currencyfx_edit';
    final public const MOD_ADMIN_CURRENCYFX_CREATE = 'admin_currencyfx_create';
    final public const MOD_ADMIN_CURRENCY_DELETE = 'admin_currencyfx_delete';

    // DefaultController
    final public const MOD_ADMIN_HOME = 'admin_home';
    final public const MOD_ADMIN_FOS_USER_PROFILE_SHOW = 'fos_user_profile_show';
    final public const MOD_HEALTH_CHECK = 'healthcheck';

    // DiscountController
    final public const MOD_ADMIN_DISCOUNT_CODES = 'admin_discountcodes';
    final public const MOD_ADMIN_DISCOUNT_CODES_CREATE = 'admin_discountcodes_create';
    final public const MOD_ADMIN_DISCOUNT_CODES_EDIT = 'admin_discountcodes_edit';
    final public const MOD_ADMIN_DISCOUNT_CODES_GET = 'admin_discountcodes_get';
    final public const MOD_ADMIN_DISCOUNT_CODES_DELETE = 'admin_discountcodes_delete';

    // DynamicCodeController
    final public const MOD_ADMIN_DYNAMIC_CODE_LIST = 'admin_dynamiccode_list';
    final public const MOD_ADMIN_DYNAMIC_CODE_GET = 'admin_dynamiccode_get';
    final public const MOD_ADMIN_DYNAMIC_CODE_EDIT = 'admin_dynamiccode_edit';
    final public const MOD_ADMIN_DYNAMIC_CODE_CREATE = 'admin_dynamiccode_create';
    final public const MOD_ADMIN_DYNAMIC_CODE_DELETE = 'admin_dynamiccode_delete';

    // BatchDynamicCodeController
    final public const MOD_ADMIN_BATCH_DYNAMIC_CODE_LIST = 'admin_batch_dynamiccode_list';
    final public const MOD_ADMIN_BATCH_DYNAMIC_CODE_GET = 'admin_batch_dynamiccode_get';
    final public const MOD_ADMIN_BATCH_DYNAMIC_CODE_EDIT = 'admin_batch_dynamiccode_edit';
    final public const MOD_ADMIN_BATCH_DYNAMIC_CODE_CREATE = 'admin_batch_dynamiccode_create';
    final public const MOD_ADMIN_BATCH_DYNAMIC_CODE_DELETE = 'admin_batch_dynamiccode_delete';
    final public const MOD_ADMIN_BATCH_DYNAMIC_CODE_DOWNLOAD_ZIP = 'admin_dynamicCode_download_zip';

    // FlowController
    final public const MOD_ADMIN_ONBORADING_FLOWS = 'admin_onboarding_flows';
    final public const MOD_ADMIN_ONBOARDING_FLOWS_CREATE = 'admin_onboarding_flows_create';
    final public const MOD_ADMIN_ONBOARDING_FLOWS_DETAIL = 'admin_onboarding_flows_detail';
    final public const MOD_ADMIN_ONBOARDING_FLOWS_DELETE = 'admin_onboarding_flows_delete';

    // FxAnalyticsControllers
    final public const MOD_ADMIN_CURRENCYFX_ANALYTICS = 'admin_currencyfx_analytics';

    // FXOrderController
    final public const MOD_ADMIN_CURRENCYFX_ORDERS = 'admin_currencyfx_orders';
    final public const MOD_ADMIN_CURRENCYFX_ORDERS_GET = 'admin_currencyfx_orders_get';

    // InvitationController
    final public const MOD_ADMIN_ONBOARDING_INVITATIONS = 'admin_onboarding_invitations';
    final public const MOD_ADMIN_ONBOARDING_INVITATIONS_CREATE = 'admin_onboarding_invitations_create';
    final public const MOD_ADMIN_ONBOARDING_INVITATIONS_DELETE = 'admin_onboarding_invitations_delete';

    // /LocationController
    final public const MOD_ADMIN_LOCATIONS = 'admin_locations';
    final public const MOD_ADMIN_LOCATION_EDIT = 'admin_locations_edit';
    final public const MOD_ADMIN_LOCATION_PRODUCTS = 'admin_locations_products';
    final public const MOD_ADMIN_LOCATION_EXPORT = 'admin_location_export';

    // LogController
    final public const MOD_ADMIN_CONFIGURATION_LOGS = 'admin_configuration_logs';

    // ManagerPortalRolesController
    final public const MOD_ADMIN_MANAGER_PORTAL_ROLES_LISTS = 'admin_manager_portal_roles_lists';
    final public const MOD_ADMIN_MANAGER_PORTAL_ROLES_CREATE = 'admin_manger_portal_roles_create';
    final public const MOD_ADMIN_MANAGER_PORTAL_ROLES_EDIT = 'admin_manager_portal_roles_edit';
    final public const MOD_ADMIN_MANAGER_PORTAL_ROLES_DETAIL = 'admin_manager_portal_detail';
    final public const MOD_ADMIN_MANAGER_PORTAL_ROLES_DELETE = 'admin_manager_portal_delete';

    // MerchantUserController
    final public const MOD_ADMIN_MERCHANTS_USERS_SHOW = 'admin_merchants_users_show';
    final public const MOD_ADMIN_MERCHANTS_EDIT_USER_COMPLIANCE = 'admin_merchants_edit_user_compliance';
    final public const MOD_ADMIN_MERCHANTS_RESET_USER_PASSWORD = 'admin_merchants_reset_user_password';
    final public const MOD_ADMIN_MERCHANTS_SEND_TEMP_PASSWORD_USER = 'admin_merchants_send_temp_password_user';
    final public const MOD_ADMIN_MERCHANTS_ADD_COGNITO_USER = 'admin_merchants_add_cognito_user';
    final public const MOD_ADMIN_MERCHANTS_ADD_COGNITO_USER_STEP_1 = 'admin_merchants_add_cognito_user_step_1';
    final public const MOD_ADMIN_MERCHANTS_ADD_COGNITO_USER_STEP_2 = 'admin_merchants_add_cognito_user_step_2';
    final public const MOD_ADMIN_MERCHANTS_DELETE_CONFIRMATION_COGNITO_USER = 'delete_confirmation_cognito_user';
    final public const MOD_ADMIN_MERCHANTS_EDIT_USER = 'admin_merchants_edit_user';
    final public const MOD_ADMIN_MERCHANTS_DELETE_USER = 'admin_merchants_delete_user';
    final public const MOD_ADMIN_DELETE_MERCHANT_DELETE_USER_COMMIT = 'admin_merchants_delete_user_commit';
    final public const MOD_ADMIN_MERCHANTS_DETACH_USER_COMMIT = 'admin_merchants_detach_user_commit';
    final public const MOD_ADMIN_MERCHANTS_SEND_SMS = 'communication_sms';
    final public const MOD_ADMIN_MERCHANTS_COMMUNICATION_EMAIL = 'communication_email';
    final public const MOD_ADMIN_MERCHANTS_SEND_PUSH_NOTI = 'communication_pn';

    // Product Controller
    final public const MOD_ADMIN_PRODUCT = 'admin_products';

    // ProviderOnBoardingController
    final public const MOD_ADMIN_MERCHANTS_PROVIDER_DISABLED = 'admin_merchants_provider_disabled';
    final public const MOD_ADMIN_MERCHANTS_PROVIDER_CHECKS = 'admin_merchants_provider_checks';
    final public const MOD_ADMIN_MERCHANTS_PROVIDER_BULK_ONBOARDING = 'admin_merchants_provider_bulk_onboarding';
    final public const MOD_ADMIN_MERCHANTS_PROVIDER_ENABLED = 'admin_merchants_provider_enable';

    // RegistrationController
    final public const MOD_ADMIN_ONBOARDING_REGISTRATIONS = 'admin_onboarding_registrations';
    final public const MOD_ADMIN_ONBOARDING_REGISTRATIONS_DELETE = 'admin_onboarding_registrations_delete';

    // RemittanceAdviceController
    final public const MOD_ADMIN_REMA = 'admin_rema';

    // RemittanceController
    final public const MOD_ADMIN_REMITTANCES = 'admin_remittances';
    final public const MOD_ADMIN_REMITTANCES_GET = 'admin_remittances_get';
    final public const MOD_ADMIN_REMITTANCES_MARK = 'admin_remittances_mark';

    // RoleController
    final public const MOD_ADMIN_CONFIGURATIONS_ROLES = 'admin_configuration_roles';
    final public const MOD_ADMIN_CONFIGURATIONS_ROLES_CREATE = 'admin_configuration_roles_create';
    final public const MOD_ADMIN_CONFIGURATIONS_ROLES_EDIT = 'admin_configuration_roles_edit';
    final public const MOD_ADMIN_CONFIGURATIONS_ROLES_DELETE = 'admin_configuration_roles_delete';

    // SubscriptionPlanController
    final public const MOD_ADMIN_SUBSCRIPTION_PLANS = 'admin_subscription_plans';
    final public const MOD_ADMIN_SUBSCRIPTION_PLANS_EDIT = 'admin_subscription_plans_edit';
    final public const MOD_ADMIN_SUBSCRIPTION_PLANS_DELETE = 'admin_subscription_plans_delete';

    // TipsAndTricksController
    final public const MOD_ADMIN_TIPS_AND_TRIPS = 'admin_tips_and_trips';
    final public const MOD_ADMIN_TIPS_AND_TOGGLE = 'admin_tips_and_trips_toggle';
    final public const MOD_ADMIN_TIPS_AND_CREATE = 'admin_tips_and_trips_create';
    final public const MOD_ADMIN_TIPS_AND_DETAILS = 'admin_tips_and_trips_details';
    final public const MOD_ADMIN_TIPS_AND_EDIT = 'admin_tips_and_trips_edit';
    final public const MOD_ADMIN_TIPS_AND_DELETE = 'admin_tips_and_trips_delete';

    // TransactionController
    final public const MOD_ADMIN_TRANSACTIONS = 'admin_transactions';
    final public const MOD_ADMIN_TRANSACTIONS_LISTS = 'admin_transactions_lists';
    final public const MOD_ADMIN_TRANSACTIONS_GET = 'admin_transactions_get';
    final public const MOD_ADMIN_TRANSACTIONS_APPROVE_REFUND = 'admin_transactions_approve_refund';
    final public const MOD_ADMIN_TRANSACTIONS_EXPORT = 'admin_transactions_export';
    final public const MOD_ADMIN_TRANSACTIONS_STATE = 'admin_transactions_state';
    final public const MOD_ADMIN_TRANSACTIONS_EDIT = 'admin_transactions_edit';
    final public const MOD_ADMIN_TRANSACTIONS_CREATE = 'admin_transactions_create';
    final public const MOD_ADMIN_TRANSACTIONS_DOWNLOAD_ATTACHMENT = 'download_attachment';

    // UserController
    final public const MOD_ADMIN_USERS = 'admin_users';
    final public const MOD_ADMIN_USERS_CREATE = 'admin_users_create';
    final public const MOD_ADMIN_USERS_EDIT = 'admin_users_edit';
    final public const MOD_ADMIN_USERS_DISABLE = 'admin_users_disable';
    final public const MOD_ADMIN_USERS_ENABLE = 'admin_users_enable';
    final public const MOD_ADMIN_USERS_EXPORT = 'admin_users_export';
    final public const MOD_ADMIN_USERS_2FA_RESET = 'admin_users_2fa_reset';

    // CirclesController
    final public const MOD_CIRCLES_LISTS = 'admin_merchant_circles';
    final public const MOD_CIRCLES_CREATE = 'admin_merchant_circles_create';
    final public const MOD_CIRCLES_DETAIL = 'admin_merchant_circles_get';
    final public const MOD_CIRCLES_EDIT = 'admin_merchant_circles_edit';
    final public const MOD_CIRCLES_DELETE = 'admin_merchant_circles_delete';

    // ReportController
    final public const MOD_MY_REPORT = 'my_reports';
    final public const MOD_REPORT_REQUEST = 'request_report';
    final public const MOD_GET_REPORT_DETAIL = 'get_report_detail';
    final public const MOD_DELETE_REPORT_DETAIL = 'delete_my_report';

    // REPORTING MODULES
    final public const REPORT_MERCHANT_MODULE = 'export_merchant_report';
    final public const REPORT_TRANSACTION_MODULE = 'export_transaction_report';
    final public const REPORT_COGNITO_LIST = 'cognito_list';
    final public const REPORT_LOCATION_MODULE = 'export_location_report';
    final public const REPORT_BENEFIT_MODULE = 'export_benefit_report';
    final public const REPORT_SINGLE_TRANSACTION_REPORT_MODULE = 'export_single_transaction_report';
    final public const REPORT_PLATFORM_BILLING = 'export_platform_billing_report';
    final public const REPORT_AUTOCREDIT = 'export_autocredit_report';
    final public const REPORT_REMITTANCE = 'export_remittance_report';

    // PayoutReportController
    final public const ADMIN_PAYOUTREPORT_LIST = 'admin_reports_payoutreports';
    final public const ADMIN_PAYOUTREPORT_GET = 'admin_reports_payoutreports_get';
    final public const ADMIN_PAYOUTREPORT_DOWNLOAD = 'admin_reports_payoutreports_download';

    // DISPUTES
    final public const MOD_ADMIN_COMPLIANCE_DISPUTES_LIST = 'admin_compliance_disputes_list';
    final public const MOD_ADMIN_COMPLIANCE_DISPUTES_GET = 'admin_compliance_disputes_get';
    final public const MOD_ADMIN_COMPLIANCE_DISPUTES_CREATE = 'admin_compliance_disputes_create';

    // Terms
    final public const MOD_ADMIN_CONFIGURATIONS_TERMS = 'admin_configuration_terms';
    final public const MOD_ADMIN_CONFIGURATIONS_TERMS_CREATE = 'admin_configuration_terms_create';

    /**
     * @return array<string, string|string[]|null>
     */
    public static function permissionMap(): array
    {
        return [
            self::MOD_MERCHANTS_ADD_COMPLIANCE_DOCUMENT => Permission::MERCHANT_COMPLIANCE.Action::CREATE,
            self::MOD_MERCHANTS_COMPLIANCE_DOWNLOAD_DOCUMENT => Permission::MERCHANT_COMPLIANCE.Action::DOWNLOAD,
            self::MOD_VIEW_MERCHANTS => Permission::MODULE_MERCHANT.Action::VIEW,
            self::MOD_VIEW_INDIVIDUAL_MERCHANT => [
                Permission::MODULE_MERCHANT.Action::VIEW,
                Permission::MERCHANT_OVERVIEW.Action::VIEW,
                Permission::MERCHANT_BANK_ACCOUNT.Action::VIEW,
                Permission::MERCHANT_BRANDING.Action::VIEW,
                Permission::MERCHANT_DETAILS.Action::VIEW,
                Permission::MERCHANT_STRUCTURE.Action::VIEW,
                Permission::MERCHANT_USERS.Action::VIEW,
                Permission::MERCHANT_TERMS.Action::VIEW,
                Permission::MERCHANT_FINANCIAL.Action::VIEW,
                Permission::MERCHANT_LOCATIONS.Action::VIEW,
                Permission::MERCHANT_NOTES.Action::VIEW,
                Permission::MERCHANT_PAYMENT_METHOD.Action::VIEW,
                Permission::MERCHANT_DATA.Action::VIEW,
                Permission::MERCHANT_EVENTS.Action::VIEW,
                Permission::MERCHANT_PUSH_NOTIFICATION.Action::VIEW,
                Permission::MERCHANT_EMAIL.Action::VIEW,
                Permission::MERCHANT_SMS.Action::VIEW,
                Permission::MERCHANT_TERMS.Action::VIEW,
                Permission::MERCHANT_COMPLIANCE.Action::VIEW,
            ],
            self::MOD_MERCHANTS_EDIT_REVIEW_STATUS => Permission::MERCHANT_FINANCIAL.Action::EDIT,
            self::MOD_MERCHANTS_EDIT_ADDRESS => Permission::MERCHANT_DETAILS.Action::VIEW,
            self::MOD_MERCHANTS_EDIT_BRANDING => Permission::MERCHANT_BRANDING.Action::EDIT,
            self::MOD_MERCHANTS_EDIT_PAYMENTS => null,
            self::MOD_MERCHANTS_EDIT_RESELLER_METADATA => Permission::MERCHANT_DATA.Action::EDIT,
            self::MOD_MERCHANTS_CONFIRM_DELETE => null,
            self::MOD_ADMIN_MERCHANTS_SEND_EMAIL => Permission::MERCHANT_EMAIL.Action::CREATE,
            self::MOD_MERCHANTS_DELETE => Permission::MODULE_MERCHANT.Action::DELETE,
            self::MOD_MERCHANTS_EDIT_STRUCTURE => [
                Permission::MERCHANT_STRUCTURE.Action::VIEW,
                Permission::MERCHANT_STRUCTURE.Action::EDIT,
                Permission::MERCHANT_STRUCTURE.Action::DELETE,
                Permission::MERCHANT_STRUCTURE.Action::CREATE,
            ],
            self::MOD_MERCHANTS_EDIT_SUBSCRIPTION_PLAN => Permission::MERCHANT_FINANCIAL.Action::EDIT,
            self::MOD_MERCHANTS_REPORTS_EXPORT => Permission::REPORTS_MERCHANTS.Action::DOWNLOAD,
            self::MOD_MERCHANTS_EDIT_BANK_ACCOUNTS => [
                Permission::MERCHANT_BANK_ACCOUNT.Action::VIEW,
                Permission::MERCHANT_BANK_ACCOUNT.Action::CREATE,
                Permission::MERCHANT_BANK_ACCOUNT.Action::EDIT,
                Permission::MERCHANT_BANK_ACCOUNT.Action::DELETE,
            ],
            self::MOD_ADDONS_BETAFEATUES => Permission::CONFIGURATION_BETA_FEATURE.Action::VIEW,
            self::MOD_ADDONS_BETAFEATURES_CREATE => Permission::CONFIGURATION_BETA_FEATURE.Action::CREATE,
            self::MOD_ADDONS_BETAFEATURES_EDIT => Permission::CONFIGURATION_BETA_FEATURE.Action::EDIT,
            self::MOD_ADDONS_BETAFEATURES_GET => Permission::CONFIGURATION_BETA_FEATURE.Action::VIEW,
            self::MOD_ADDONS_BETAFEATURES_DELETE => Permission::CONFIGURATION_BETA_FEATURE.Action::DELETE,
            self::MOD_ADMIN_MERCHANT_NOTE_CREATE => Permission::MERCHANT_NOTES.Action::CREATE,
            self::MOD_ADMIN_MERCHANT_NOTE_TOGGLE => Permission::MERCHANT_NOTES.Action::COMPLETE,
            self::MOD_ADMIN_COMPLIANCE_LIST => Permission::COMPLIANCE_CASE.Action::VIEW,
            self::MOD_ADMIN_COMPLIANCE_MY_WORKFLOW => Permission::COMPLIANCE_CASE.Action::VIEW,
            self::MOD_ADMIN_COMPLIANCE_CREATE => Permission::COMPLIANCE_CASE.Action::CREATE,
            self::MOD_ADMIN_COMPLIANCE_EDIT => Permission::COMPLIANCE_CASE.Action::EDIT,
            self::MOD_ADMIN_COMPLIANCE_ASSIGN_HANDLER => Permission::COMPLIANCE_CASE.Action::ASSIGN,
            self::MOD_ADMIN_COMPLIANCE_ASSIGN_APPROVER => Permission::COMPLIANCE_CASE.Action::APPROVE,
            self::MOD_ADMIN_COMPLIANCE_DELETE => Permission::COMPLIANCE_CASE.Action::DELETE,
            self::MOD_ADMIN_COMPLIANCE_ADMIN => Permission::COMPLIANCE_CASE.Action::ANY,
            self::MOD_ADMIN_COMPLIANCE_PROFILES => Permission::COMPLIANCE_RISK_PROFILE.Action::VIEW,
            self::MOD_ADMIN_COMPLIANCE_PROFILES_GET => Permission::COMPLIANCE_RISK_PROFILE.Action::VIEW,
            self::MOD_ADMIN_COMPLIANCE_PROFILES_EDIT => Permission::COMPLIANCE_RISK_PROFILE.Action::EDIT,
            self::MOD_ADMIN_COMPLIANCE_PROFILES_CREATE => Permission::COMPLIANCE_RISK_PROFILE.Action::CREATE,
            self::MOD_ADMIN_COMPLIANCE_PROFILES_DELETE => Permission::COMPLIANCE_RISK_PROFILE.Action::DELETE,
            self::MOD_ADMIN_CONFIG_INTEGRATION => Permission::CONFIGURATION_INTEGRATION.Action::VIEW,
            self::MOD_ADMIN_CONFIG_INTEGRATION_GET => Permission::CONFIGURATION_INTEGRATION.Action::VIEW,
            self::MOD_ADMIN_CONFIG_INTEGRATION_EDIT => Permission::CONFIGURATION_INTEGRATION.Action::EDIT,
            self::MOD_ADMIN_CONFIG_INTEGRATION_CREATE => Permission::CONFIGURATION_INTEGRATION.Action::CREATE,
            self::MOD_ADMIN_CONFIG_INTEGRATION_DELETE => Permission::CONFIGURATION_INTEGRATION.Action::DELETE,
            self::MOD_ADMIN_CONFIG_MDR_BILLING => Permission::CONFIGURATION_MDR_BILLING.Action::VIEW,
            self::MOD_ADMIN_CONFIG_MDR_BILLING_GET => Permission::CONFIGURATION_MDR_BILLING.Action::VIEW,
            self::MOD_ADMIN_CONFIG_MDR_BILLING_EDIT => Permission::CONFIGURATION_MDR_BILLING.Action::EDIT,
            self::MOD_ADMIN_CONFIG_MDR_BILLING_CREATE => Permission::CONFIGURATION_MDR_BILLING.Action::CREATE,
            self::MOD_ADMIN_CONFIG_MDR_BILLING_DELETE => Permission::CONFIGURATION_MDR_BILLING.Action::DELETE,
            self::MOD_ADMIN_CURRENCYFX_DCC_REBATE_REPORTS => Permission::CURRENCY_FX_DCC_REBATE_REPORTS.Action::VIEW,
            self::MOD_ADMIN_CURRENCYFX_DCC_REBATE_REPORTS_GET => Permission::CURRENCY_FX_DCC_REBATE_REPORTS.Action::VIEW,
            self::MOD_ADMIN_CURRENCYFX_SETTLEMENTS => Permission::CURRENCY_FX_SETTLEMENT.Action::VIEW,
            self::MOD_ADMIN_CURRENCYFX_SETTLEMENTS_GET => Permission::CURRENCY_FX_SETTLEMENT.Action::VIEW,
            self::MOD_ADMIN_CURRENCYFX_SETTLEMENTS_EDIT => Permission::CURRENCY_FX_SETTLEMENT.Action::EDIT,
            self::MOD_ADMIN_OFFERS_BENEFITS => Permission::OFFER_BENEFIT.Action::VIEW,
            self::MOD_ADMIN_OFFERS_BENEFITS_GET => Permission::OFFER_BENEFIT.Action::VIEW,
            self::MOD_ADMIN_OFFERS_BENEFITS_EDIT => Permission::OFFER_BENEFIT.Action::EDIT,
            self::MOD_ADMIN_OFFERS_BENEFITS_CREATE => Permission::OFFER_BENEFIT.Action::CREATE,
            self::MOD_ADMIN_OFFERS_BENEFITS_DELETE => Permission::OFFER_BENEFIT.Action::DELETE,
            self::MOD_ADMIN_LINKED_RESOURCE_ROUTE => Permission::OFFER_BENEFIT.Action::DELETE,
            self::MOD_ADMIN_OFFERS_BENEFITS_TOGGLE => Permission::OFFER_BENEFIT.Action::EDIT,
            self::MOD_ADMIN_OFFERS_BLOCKS => Permission::OFFER_BLOCK.Action::VIEW,
            self::MOD_ADMIN_OFFERS_BLOCKS_GET => Permission::OFFER_BLOCK.Action::VIEW,
            self::MOD_ADMIN_OFFERS_BLOCKS_EDIT => Permission::OFFER_BLOCK.Action::EDIT,
            self::MOD_ADMIN_OFFERS_BLOCKS_CREATE => Permission::OFFER_BLOCK.Action::CREATE,
            self::MOD_ADMIN_OFFERS_BLOCKS_DELETE => Permission::OFFER_BLOCK.Action::DELETE,
            self::MOD_ADMIN_OFFERS_BRANDS => Permission::OFFER_BRAND.Action::VIEW,
            self::MOD_ADMIN_OFFERS_BRANDS_GET => Permission::OFFER_BRAND.Action::VIEW,
            self::MOD_ADMIN_OFFERS_BRANDS_EDIT => Permission::OFFER_BRAND.Action::EDIT,
            self::MOD_ADMIN_OFFERS_BRANDS_CREATE => Permission::OFFER_BRAND.Action::CREATE,
            self::MOD_ADMIN_OFFERS_BRANDS_DELETE => Permission::OFFER_BRAND.Action::DELETE,
            self::MOD_ADMIN_OFFERS_CARDS => Permission::OFFER_CARDS.Action::VIEW,
            self::MOD_ADMIN_OFFERS_CARDS_GET => Permission::OFFER_CARDS.Action::VIEW,
            self::MOD_ADMIN_OFFERS_CARDS_EDIT => Permission::OFFER_CARDS.Action::EDIT,
            self::MOD_ADMIN_OFFERS_CARDS_CREATE => Permission::OFFER_CARDS.Action::CREATE,
            self::MOD_ADMIN_OFFERS_CARDS_DELETE => Permission::OFFER_CARDS.Action::DELETE,
            self::MOD_ADMIN_OFFERS_CATEGORIES => Permission::OFFER_CATEGORY.Action::VIEW,
            self::MOD_ADMIN_OFFERS_CATEGORIES_GET => Permission::OFFER_CATEGORY.Action::VIEW,
            self::MOD_ADMIN_OFFERS_CATEGORIES_EDIT => Permission::OFFER_CATEGORY.Action::EDIT,
            self::MOD_ADMIN_OFFERS_CATEGORIES_CREATE => Permission::OFFER_CATEGORY.Action::CREATE,
            self::MOD_ADMIN_OFFERS_CATEGORIES_DELETE => Permission::OFFER_CATEGORY.Action::DELETE,
            self::MOD_ADMIN_OFFERS_DEALS => Permission::OFFER_DEAL.Action::VIEW,
            self::MOD_ADMIN_OFFERS_DEALS_GET => Permission::OFFER_DEAL.Action::VIEW,
            self::MOD_ADMIN_OFFERS_DEALS_EDIT => Permission::OFFER_DEAL.Action::EDIT,
            self::MOD_ADMIN_OFFERS_DEALS_CREATE => Permission::OFFER_DEAL.Action::CREATE,
            self::MOD_ADMIN_OFFERS_DEALS_DELETE => Permission::OFFER_DEAL.Action::DELETE,
            self::MOD_ADMIN_OFFERS_DEALS_TOGGLE => Permission::OFFER_DEAL.Action::EDIT,
            self::MOD_ADMIN_OFFERS_DEALS_DELETE_IMAGE => Permission::OFFER_DEAL.Action::DELETE,
            self::MOD_ADMIN_REPORTS_PLATFORMBILLING => Permission::REPORTS_PLATFORM_BILLING.Action::VIEW,
            self::MOD_ADMIN_REPROTS_PLATFORMBILLING_GET => Permission::REPORTS_PLATFORM_BILLING.Action::VIEW,
            self::MOD_ADMIN_TRANSACTIONS_REPORTS => Permission::REPORTS_TRANSACTION.Action::VIEW,
            self::MOD_ADMIN_TRANSACTIONS_REPORT_GET => Permission::REPORTS_TRANSACTION.Action::VIEW,
            self::MOD_ADMIN_TRANSACTIONS_REPORTS_DOWNLOAD => Permission::REPORTS_TRANSACTION.Action::DOWNLOAD,
            self::MOD_ADMIN_SYSTEM_COGNITO_LISTS => Permission::ONBOARDING_FEDERATED_IDENTITY.Action::VIEW,
            self::MOD_ADMIN_CONFIGURATION_STATUS => Permission::CONFIGURATION_API_STATUS.Action::VIEW,
            self::MOD_ADMIN_CONFIGURATION_STATUS_CREATE => Permission::CONFIGURATION_API_STATUS.Action::CREATE,
            self::MOD_ADMIN_CONFIGURATION_STATUS_EDIT => Permission::CONFIGURATION_API_STATUS.Action::EDIT,
            self::MOD_ADMIN_CONFIGURATION_STATUS_DELETE => Permission::CONFIGURATION_API_STATUS.Action::DELETE,
            self::MOD_ADMIN_APPS => Permission::CONFIGURATION_APPS.Action::VIEW,
            self::MOD_AUTOCREDITS => Permission::REPORTS_AUTOCREDIT.Action::VIEW,
            self::MOD_AUTOCREDITS_DOWNLOAD => Permission::REPORTS_AUTOCREDIT.Action::DOWNLOAD,
            self::MOD_ADMIN_CARDS => Permission::OFFER_CARDS.Action::VIEW,
            self::MOD_ADMIN_MERCHANTS_LISTS => Permission::MODULE_MERCHANT.Action::VIEW,
            self::MOD_ADMIN_CONFIGURATION_PROVIDERS => Permission::CONFIGURATION_PROVIDERS.Action::VIEW,
            self::MOD_ADMIN_CONFIGURATION_PROVIDERS_CREATE => Permission::CONFIGURATION_PROVIDERS.Action::CREATE,
            self::MOD_ADMIN_CONFIGURATION_PROVIDERS_EDIT => Permission::CONFIGURATION_PROVIDERS.Action::EDIT,
            self::MOD_ADMIN_CONFIGURATION_COUNTRIES => Permission::CONFIGURATION_COUNTRY.Action::VIEW,
            self::MOD_ADMIN_CONFIGURATION_COUNTRIES_CREATE => Permission::CONFIGURATION_COUNTRY.Action::CREATE,
            self::MOD_ADMIN_CONFIGURATION_COUNTRIES_EDIT => Permission::CONFIGURATION_COUNTRY.Action::EDIT,
            self::MOD_ADMIN_CONFIGURATION_SETTINGS => Permission::CONFIGURATION_SETTINGS.Action::VIEW,
            self::MOD_ADMIN_CONFIGURATION_SETTINGS_CREATE => Permission::CONFIGURATION_SETTINGS.Action::CREATE,
            self::MOD_ADMIN_CONFIGURATION_SETTINGS_DELETE => Permission::CONFIGURATION_SETTINGS.Action::DELETE,
            self::MOD_ADMIN_CONFIGURATION_SETTING_EDIT => Permission::CONFIGURATION_SETTINGS.Action::EDIT,
            self::MOD_ADMIN_PROVISION_ACTION => Permission::MODULE_MERCHANT.Action::CREATE,
            self::MOD_ADMIN_CURRENCYFX => Permission::CURRENCY_FX_RATES.Action::VIEW,
            self::MOD_ADMIN_CURRENCYFX_GET => Permission::CURRENCY_FX_RATES.Action::VIEW,
            self::MOD_ADMIN_CURRENCYFX_EDIT => Permission::CURRENCY_FX_RATES.Action::EDIT,
            self::MOD_ADMIN_CURRENCYFX_CREATE => Permission::CURRENCY_FX_RATES.Action::CREATE,
            self::MOD_ADMIN_CURRENCY_DELETE => Permission::CURRENCY_FX_RATES.Action::DELETE,
            self::MOD_ADMIN_HOME => Permission::MODULE_ADMINISTRATORS.Action::VIEW,
            self::MOD_ADMIN_FOS_USER_PROFILE_SHOW => Permission::MODULE_ADMINISTRATORS.Action::VIEW,
            self::MOD_HEALTH_CHECK => Permission::MODULE_ADMINISTRATORS.Action::VIEW,
            self::MOD_ADMIN_DISCOUNT_CODES => Permission::CONFIGURATION_DISCOUNT_CODE.Action::VIEW,
            self::MOD_ADMIN_DISCOUNT_CODES_CREATE => Permission::CONFIGURATION_DISCOUNT_CODE.Action::CREATE,
            self::MOD_ADMIN_DISCOUNT_CODES_EDIT => Permission::CONFIGURATION_DISCOUNT_CODE.Action::EDIT,
            self::MOD_ADMIN_DISCOUNT_CODES_GET => Permission::CONFIGURATION_DISCOUNT_CODE.Action::VIEW,
            self::MOD_ADMIN_DISCOUNT_CODES_DELETE => Permission::CONFIGURATION_DISCOUNT_CODE.Action::DELETE,
            self::MOD_ADMIN_DYNAMIC_CODE_LIST => Permission::ONBOARDING_DYNAMIC_CODE.Action::VIEW,
            self::MOD_ADMIN_DYNAMIC_CODE_GET => Permission::ONBOARDING_DYNAMIC_CODE.Action::VIEW,
            self::MOD_ADMIN_DYNAMIC_CODE_EDIT => Permission::ONBOARDING_DYNAMIC_CODE.Action::EDIT,
            self::MOD_ADMIN_DYNAMIC_CODE_CREATE => Permission::ONBOARDING_DYNAMIC_CODE.Action::CREATE,
            self::MOD_ADMIN_DYNAMIC_CODE_DELETE => Permission::ONBOARDING_DYNAMIC_CODE.Action::DELETE,
            self::MOD_ADMIN_BATCH_DYNAMIC_CODE_LIST => Permission::ONBOARDING_BATCH_DYNAMIC_CODE.Action::VIEW,
            self::MOD_ADMIN_BATCH_DYNAMIC_CODE_GET => Permission::ONBOARDING_BATCH_DYNAMIC_CODE.Action::VIEW,
            self::MOD_ADMIN_BATCH_DYNAMIC_CODE_EDIT => Permission::ONBOARDING_BATCH_DYNAMIC_CODE.Action::EDIT,
            self::MOD_ADMIN_BATCH_DYNAMIC_CODE_CREATE => Permission::ONBOARDING_BATCH_DYNAMIC_CODE.Action::CREATE,
            self::MOD_ADMIN_BATCH_DYNAMIC_CODE_DELETE => Permission::ONBOARDING_BATCH_DYNAMIC_CODE.Action::DELETE,
            self::MOD_ADMIN_BATCH_DYNAMIC_CODE_DOWNLOAD_ZIP => Permission::ONBOARDING_BATCH_DYNAMIC_CODE.Action::DOWNLOAD,
            self::MOD_ADMIN_ONBORADING_FLOWS => Permission::ONBOARDING_FLOWS.Action::VIEW,
            self::MOD_ADMIN_ONBOARDING_FLOWS_CREATE => Permission::ONBOARDING_FLOWS.Action::CREATE,
            self::MOD_ADMIN_ONBOARDING_FLOWS_DELETE => Permission::ONBOARDING_FLOWS.Action::DELETE,
            self::MOD_ADMIN_ONBOARDING_FLOWS_DETAIL => Permission::ONBOARDING_FLOWS.Action::VIEW,
            self::MOD_ADMIN_CURRENCYFX_ANALYTICS => null,
            self::MOD_ADMIN_CURRENCYFX_ORDERS => Permission::CURRENCY_FX_ORDERS.Action::VIEW,
            self::MOD_ADMIN_CURRENCYFX_ORDERS_GET => Permission::CURRENCY_FX_ORDERS.Action::VIEW,
            self::MOD_ADMIN_ONBOARDING_INVITATIONS => Permission::ONBOARDING_INVITATIONS.Action::VIEW,
            self::MOD_ADMIN_ONBOARDING_INVITATIONS_CREATE => Permission::ONBOARDING_INVITATIONS.Action::CREATE,
            self::MOD_ADMIN_ONBOARDING_INVITATIONS_DELETE => Permission::ONBOARDING_INVITATIONS.Action::DELETE,
            self::MOD_ADMIN_LOCATIONS => Permission::MARKETPLACE_LOCATION.Action::VIEW,
            self::MOD_ADMIN_LOCATION_EDIT => Permission::MARKETPLACE_LOCATION.Action::EDIT,
            self::MOD_ADMIN_LOCATION_PRODUCTS => Permission::MARKETPLACE_LOCATION.Action::VIEW,
            self::MOD_ADMIN_LOCATION_EXPORT => Permission::REPORTS_LOCATIONS.Action::DOWNLOAD,
            self::MOD_ADMIN_CONFIGURATION_LOGS => Permission::CONFIGURATION_LOGS.Action::VIEW,
            self::MOD_ADMIN_MANAGER_PORTAL_ROLES_LISTS => Permission::CONFIGURATION_MANAGER_ROLES.Action::VIEW,
            self::MOD_ADMIN_MANAGER_PORTAL_ROLES_CREATE => Permission::CONFIGURATION_MANAGER_ROLES.Action::CREATE,
            self::MOD_ADMIN_MANAGER_PORTAL_ROLES_EDIT => Permission::CONFIGURATION_MANAGER_ROLES.Action::EDIT,
            self::MOD_ADMIN_MANAGER_PORTAL_ROLES_DETAIL => Permission::CONFIGURATION_MANAGER_ROLES.Action::VIEW,
            self::MOD_ADMIN_MANAGER_PORTAL_ROLES_DELETE => Permission::CONFIGURATION_MANAGER_ROLES.Action::DELETE,
            self::MOD_ADMIN_MERCHANTS_USERS_SHOW => Permission::MERCHANT_USERS.Action::VIEW,
            self::MOD_ADMIN_MERCHANTS_EDIT_USER_COMPLIANCE => Permission::MERCHANT_USERS.Action::EDIT,
            self::MOD_ADMIN_MERCHANTS_RESET_USER_PASSWORD => Permission::MERCHANT_USERS.Action::EDIT,
            self::MOD_ADMIN_MERCHANTS_SEND_TEMP_PASSWORD_USER => null,
            self::MOD_ADMIN_MERCHANTS_ADD_COGNITO_USER => Permission::MODULE_ONBOARDING.Action::CREATE,
            self::MOD_ADMIN_MERCHANTS_ADD_COGNITO_USER_STEP_1 => Permission::MODULE_ONBOARDING.Action::CREATE,
            self::MOD_ADMIN_MERCHANTS_ADD_COGNITO_USER_STEP_2 => Permission::MODULE_ONBOARDING.Action::CREATE,
            self::MOD_ADMIN_MERCHANTS_DELETE_CONFIRMATION_COGNITO_USER => Permission::MODULE_ONBOARDING.Action::CREATE,
            self::MOD_ADMIN_MERCHANTS_EDIT_USER => Permission::MERCHANT_USERS.Action::EDIT,
            self::MOD_ADMIN_MERCHANTS_DELETE_USER => Permission::MERCHANT_USERS.Action::DELETE,
            self::MOD_ADMIN_DELETE_MERCHANT_DELETE_USER_COMMIT => null,
            self::MOD_ADMIN_MERCHANTS_COMMUNICATION_EMAIL => Permission::MERCHANT_EMAIL.Action::CREATE,
            self::MOD_ADMIN_MERCHANTS_DETACH_USER_COMMIT => Permission::MERCHANT_USERS.Action::EDIT,
            self::MOD_ADMIN_MERCHANTS_SEND_SMS => Permission::MERCHANT_SMS.Action::CREATE,
            self::MOD_ADMIN_MERCHANTS_SEND_PUSH_NOTI => Permission::MERCHANT_PUSH_NOTIFICATION.Action::CREATE,
            self::MOD_ADMIN_PRODUCT => Permission::MARKETPLACE_PRODUCTS.Action::VIEW,
            self::MOD_ADMIN_MERCHANTS_PROVIDER_DISABLED => Permission::MERCHANT_PAYMENT_METHOD.Action::DISABLE,
            self::MOD_ADMIN_MERCHANTS_PROVIDER_CHECKS => Permission::MERCHANT_PAYMENT_METHOD.Action::VIEW,
            self::MOD_ADMIN_MERCHANTS_PROVIDER_BULK_ONBOARDING => Permission::MERCHANT_PAYMENT_METHOD.Action::ENABLE,
            self::MOD_ADMIN_MERCHANTS_PROVIDER_ENABLED => Permission::MERCHANT_PAYMENT_METHOD.Action::ENABLE,
            self::MOD_ADMIN_ONBOARDING_REGISTRATIONS => Permission::ONBOARDING_REGISTRATIONS.Action::VIEW,
            self::MOD_ADMIN_ONBOARDING_REGISTRATIONS_DELETE => Permission::ONBOARDING_REGISTRATIONS.Action::DELETE,
            self::MOD_ADMIN_REMA => null,
            self::MOD_ADMIN_REMITTANCES => Permission::MODULE_REMITTANCE.Action::VIEW,
            self::MOD_ADMIN_REMITTANCES_GET => Permission::MODULE_REMITTANCE.Action::VIEW,
            self::MOD_ADMIN_REMITTANCES_MARK => Permission::MODULE_REMITTANCE.Action::EDIT,
            self::MOD_ADMIN_CONFIGURATIONS_ROLES => Permission::CONFIGURATION_ROLES.Action::VIEW,
            self::MOD_ADMIN_CONFIGURATIONS_ROLES_CREATE => Permission::CONFIGURATION_ROLES.Action::CREATE,
            self::MOD_ADMIN_CONFIGURATIONS_ROLES_EDIT => Permission::CONFIGURATION_ROLES.Action::EDIT,
            self::MOD_ADMIN_CONFIGURATIONS_ROLES_DELETE => Permission::CONFIGURATION_ROLES.Action::DELETE,
            self::MOD_ADMIN_SUBSCRIPTION_PLANS => Permission::CONFIGURATION_SUBSCRIPTION_PLAN.Action::VIEW,
            self::MOD_ADMIN_SUBSCRIPTION_PLANS_EDIT => Permission::CONFIGURATION_SUBSCRIPTION_PLAN.Action::EDIT,
            self::MOD_ADMIN_SUBSCRIPTION_PLANS_DELETE => Permission::CONFIGURATION_SUBSCRIPTION_PLAN.Action::DELETE,
            self::MOD_ADMIN_TIPS_AND_TRIPS => Permission::ONBOARDING_TIPS_AND_TRICKS.Action::VIEW,
            self::MOD_ADMIN_TIPS_AND_TOGGLE => Permission::ONBOARDING_TIPS_AND_TRICKS.Action::EDIT,
            self::MOD_ADMIN_TIPS_AND_CREATE => Permission::ONBOARDING_TIPS_AND_TRICKS.Action::CREATE,
            self::MOD_ADMIN_TIPS_AND_DETAILS => Permission::ONBOARDING_TIPS_AND_TRICKS.Action::VIEW,
            self::MOD_ADMIN_TIPS_AND_EDIT => Permission::ONBOARDING_TIPS_AND_TRICKS.Action::EDIT,
            self::MOD_ADMIN_TIPS_AND_DELETE => Permission::ONBOARDING_TIPS_AND_TRICKS.Action::DELETE,
            self::MOD_ADMIN_TRANSACTIONS => Permission::MODULE_TRANSACTION.Action::VIEW,
            self::MOD_ADMIN_TRANSACTIONS_LISTS => Permission::MODULE_TRANSACTION.Action::VIEW,
            self::MOD_ADMIN_TRANSACTIONS_GET => Permission::MODULE_TRANSACTION.Action::VIEW,
            self::MOD_ADMIN_TRANSACTIONS_APPROVE_REFUND => null,
            self::MOD_ADMIN_TRANSACTIONS_EXPORT => Permission::REPORTS_TRANSACTION.Action::DOWNLOAD,
            self::MOD_ADMIN_TRANSACTIONS_STATE => Permission::MODULE_TRANSACTION.Action::EDIT,
            self::MOD_ADMIN_TRANSACTIONS_EDIT => Permission::MODULE_TRANSACTION.Action::EDIT,
            self::MOD_ADMIN_TRANSACTIONS_CREATE => Permission::MODULE_TRANSACTION.Action::CREATE,
            self::MOD_ADMIN_TRANSACTIONS_DOWNLOAD_ATTACHMENT => Permission::MODULE_TRANSACTION.Action::DOWNLOAD,
            self::MOD_ADMIN_USERS => Permission::MODULE_ADMINISTRATORS.Action::VIEW,
            self::MOD_ADMIN_USERS_CREATE => Permission::MODULE_ADMINISTRATORS.Action::CREATE,
            self::MOD_ADMIN_USERS_EDIT => Permission::MODULE_ADMINISTRATORS.Action::EDIT,
            self::MOD_ADMIN_USERS_DISABLE => Permission::MODULE_ADMINISTRATORS.Action::DISABLE,
            self::MOD_ADMIN_USERS_ENABLE => Permission::MODULE_ADMINISTRATORS.Action::ENABLE,
            self::MOD_ADMIN_USERS_EXPORT => Permission::MODULE_ADMINISTRATORS.Action::DOWNLOAD,
            self::MOD_ADMIN_USERS_2FA_RESET => Permission::MODULE_ADMINISTRATORS.Action::EDIT,
            self::MOD_CIRCLES_LISTS => Permission::MERCHANT_CIRCLES.Action::VIEW,
            self::MOD_CIRCLES_CREATE => Permission::MERCHANT_CIRCLES.Action::CREATE,
            self::MOD_CIRCLES_DETAIL => Permission::MERCHANT_CIRCLES.Action::VIEW,
            self::MOD_CIRCLES_EDIT => Permission::MERCHANT_CIRCLES.Action::EDIT,
            self::MOD_CIRCLES_DELETE => Permission::MERCHANT_CIRCLES.Action::DELETE,
            self::MOD_MY_REPORT => Permission::REPORTS_MY.Action::VIEW,
            self::MOD_REPORT_REQUEST => Permission::REPORTS_MY.Action::VIEW,
            self::MOD_GET_REPORT_DETAIL => Permission::REPORTS_MY.Action::VIEW,
            self::MOD_DELETE_REPORT_DETAIL => Permission::REPORTS_MY.Action::DELETE,
            self::REPORT_MERCHANT_MODULE => Permission::REPORTS_MERCHANTS.Action::DOWNLOAD,
            self::REPORT_TRANSACTION_MODULE => Permission::REPORTS_TRANSACTIONS.Action::DOWNLOAD,
            self::REPORT_COGNITO_LIST => Permission::REPORTS_FEDERATED_IDENTITY.Action::DOWNLOAD,
            self::REPORT_LOCATION_MODULE => Permission::REPORTS_LOCATIONS.Action::DOWNLOAD,
            self::REPORT_BENEFIT_MODULE => Permission::REPORTS_BENEFIT.Action::DOWNLOAD,
            self::REPORT_SINGLE_TRANSACTION_REPORT_MODULE => Permission::REPORTS_TRANSACTION.Action::DOWNLOAD,
            self::REPORT_PLATFORM_BILLING => Permission::REPORTS_PLATFORM_BILLING.Action::DOWNLOAD,
            self::REPORT_AUTOCREDIT => Permission::REPORTS_AUTOCREDIT.Action::DOWNLOAD,
            self::REPORT_REMITTANCE => Permission::REPORTS_REMITTANCE.Action::DOWNLOAD,
            self::ADMIN_PAYOUTREPORT_LIST => Permission::REPORTS_PAYOUT.Action::VIEW,
            self::ADMIN_PAYOUTREPORT_GET => Permission::REPORTS_PAYOUT.Action::VIEW,
            self::ADMIN_PAYOUTREPORT_DOWNLOAD => Permission::REPORTS_PAYOUT.Action::DOWNLOAD,
            self::MOD_ADMIN_COMPLIANCE_DISPUTES_LIST => Permission::COMPLIANCE_DISPUTE.Action::VIEW,
            self::MOD_ADMIN_COMPLIANCE_DISPUTES_GET => Permission::COMPLIANCE_DISPUTE.Action::VIEW,
            self::MOD_ADMIN_COMPLIANCE_DISPUTES_CREATE => Permission::COMPLIANCE_DISPUTE.Action::CREATE,
            self::MOD_ADMIN_MERCHANTS_TERMS_CREATE => Permission::MERCHANT_TERMS.Action::CREATE,
            self::MOD_ADMIN_CONFIGURATIONS_TERMS => Permission::CONFIGURATION_TERMS.Action::VIEW,
            self::MOD_ADMIN_CONFIGURATIONS_TERMS_CREATE => Permission::CONFIGURATION_TERMS.Action::CREATE,
        ];
    }
}
