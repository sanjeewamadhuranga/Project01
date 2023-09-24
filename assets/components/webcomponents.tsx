import { registerAsWebComponent } from "react-webcomponentify";
import TransactionList from "./Lists/TransactionList";
import CompanyList from "./Lists/Company/CompanyList";
import CirclesList from "./Lists/Company/CirclesList";
import CircleCompanyList from "./Lists/Company/CircleCompanyList";
import MerchantListing from "./Company/MerchantListing";
import { withSearch } from "./Lists/SearchableList";
import {
  EmailList,
  PushNotificationList,
  SmsList,
} from "./Lists/Notification/NotificationList";
import withIntl from "../services/intl/intlContext";
import DashboardWidgets from "./Dashboard/DashboardWidgets";
import ComplianceFileList from "./Lists/Company/ComplianceFileList";
import DirectorList from "./Lists/Company/DirectorList";
import PaymentMethodList from "./Lists/Company/PaymentMethodList";
import TransactionListing from "./Lists/TransactionListing";
import MerchantTransactionListing from "./Lists/MerchantTransactionListing";
import SimpleMerchantTransactionListing from "./Transaction/SimpleMerchantTransactionListing";
import RolesList from "./Lists/Configuration/RolesList";
import React from "react";
import { Button } from "react-bootstrap";
import UserList from "./Lists/Company/UserList";
import NotesActivity from "./Company/NotesActivity";
import AddNoteModal from "./Modals/AddNoteModal";
import EventList from "./Lists/Company/EventList";
import FederatedIdentityList from "./Lists/FederatedIdentityList";
import FederatedIdentityListing from "./Lists/FederatedIdentityListing";
import DealList from "./Lists/Offer/DealList";
import CardList from "./Lists/Offer/CardList";
import CategoryList from "./Lists/Offer/CategoryList";
import BenefitList from "./Lists/Offer/BenefitList";
import BrandList from "./Lists/Offer/BrandList";
import BlockList from "./Lists/Offer/BlockList";
import CountryList from "./Lists/Configuration/CountryList";
import BetaFeatureList from "./Lists/Configuration/BetaFeatureList";
import DiscountCodeList from "./Lists/Configuration/DiscountCodeList";
import MdrBillingList from "./Lists/Configuration/MdrBillingList";
import AppList from "./Lists/Configuration/AppsList";
import AdministratorList from "./Lists/AdministratorList";
import ApiStatusList from "./Lists/Configuration/ApiStatusList";
import IntegrationList from "./Lists/Configuration/IntegrationList";
import RulesList from "./Lists/Configuration/RulesList";
import SubscriptionPlanList from "./Lists/Configuration/SubscriptionPlanList";
import ProviderList from "./Lists/Configuration/ProviderList";
import LogList from "./Lists/Configuration/LogsList";
import RateList from "./Lists/FX/RateList";
import LocationsList from "./Lists/Marketplace/LocationsList";
import OrderList from "./Lists/FX/OrderList";
import DccRebateReportList from "./Lists/FX/DccRebateReportList";
import DccRebateReportTotalsList from "./Lists/FX/DccRebateReportTotalsList";
import ProductsList from "./Lists/Marketplace/ProductsList";
import RiskProfileList from "./Lists/Compliance/RiskProfileList";
import SettlementList from "./Lists/FX/SettlementList";
import CaseTransactionsList from "./Lists/Compliance/CaseTransactionsList";
import CaseListing from "./Lists/Compliance/CaseListing";
import PlatformBillingReportsList from "./Lists/Reports/PlatformBillingReportsList";
import PayoutReportsList from "./Lists/Reports/PayoutReportsList";
import TransactionsForReportList from "./Lists/Reports/TransactionsForReportList";
import TransactionReportList from "./Lists/Reports/TransactionReportList";
import BalanceAndRefunds from "./Company/BalanceAndRefunds";
import TipsAndTricksList from "./Lists/TipsAndTricksList";
import RemittanceListing from "./Lists/Remittance/RemittanceListing";
import RemittanceTransactionList from "./Lists/Remittance/RemittanceTransactionList";
import AnimatedIcon from "./Common/AnimatedIcon";
import MyReportsList from "./Lists/Reports/MyReportsList";
import BatchDynamicCodeList from "./Lists/BatchDynamicCodeList";
import DynamicCodeList from "./Lists/DynamicCodeList";
import DisputeList from "./Lists/Compliance/DisputeList";
import AutoCreditList from "./Lists/Reports/AutoCreditList";
import SettingsList from "./Lists/Configuration/SettingsList";
import NavSearch from "./Common/NavSearch";
import FlowList from "./Lists/Flow/FlowList";
import FlowListing from "./Lists/Flow/FlowListing";
import ManagerRolesList from "./Lists/Configuration/ManagerRolesList";
import MigrationsList from "./Lists/Configuration/MigrationsList";
import InvitationList from "./Lists/InvitationList";
import RegistrationList from "./Lists/RegistrationList";
import CompanyRemoveButton from "./Modals/CompanyRemoveButton";
import UserRemoveButton from "./Modals/UserRemoveButton";
import BankAccountList from "./Lists/Company/BankAccountList";
import BankList from "./Lists/Configuration/BankList";
import LocationListing from "./Company/LocationListing";
import AddAnotherForm from "./Flow/AddAnotherForm";
import Flow from "./Flow/Flow";
import TermsList from "./Lists/Terms/TermsList";
import MerchantActivityLogList from "./Lists/Company/MerchantActivityLogList";
import MarkAsPaidAction from "./Lists/Remittance/MarkAsPaidButton";
import DisputeNoteList from "./Lists/Compliance/DisputeNoteList";
import TerminalList from "./Lists/Terminal/TerminalList";
import MerchantRequestListing from "./Lists/Tasks/MerchantRequestListing";
import HolidayCalendarList from "./Lists/Configuration/HolidayCalendarList";

// Following list of React components will be registered as WebComponents to be injected from backend templating.
const webComponents: Record<string, React.ElementType> = {
  "dashboard-widgets": DashboardWidgets,
  "administrator-list": AdministratorList,
  "transaction-list": TransactionList,
  "transaction-listing": TransactionListing,
  "merchant-transaction-listing": MerchantTransactionListing,
  "simple-merchant-transaction-listing": SimpleMerchantTransactionListing,
  "company-list": CompanyList,
  "circles-list": CirclesList,
  "offer-deals-list": DealList,
  "offer-benefit-list": BenefitList,
  "onboarding-tips-and-tricks-list": TipsAndTricksList,
  "offer-cards-list": CardList,
  "offer-category-list": CategoryList,
  "offer-brand-list": BrandList,
  "offer-block-list": BlockList,
  "config-country-list": CountryList,
  "config-settings-list": SettingsList,
  "configuration-subscription-plan-list": SubscriptionPlanList,
  "configuration-betafeature-list": BetaFeatureList,
  "configuration-status-list": ApiStatusList,
  "configuration-terms-list": TermsList,
  "configuration-rules-list": RulesList,
  "configuration-manager-roles-list": ManagerRolesList,
  "discount-code-list": DiscountCodeList,
  "configuration-providers-list": ProviderList,
  "currency-fx-rates-list": RateList,
  "currency-fx-orders-list": OrderList,
  "currency-fx-dcc-rebate-reports-list": DccRebateReportList,
  "dcc-rebate-report-totals-list": DccRebateReportTotalsList,
  "transaction-report-list": TransactionReportList,
  "transactions-for-report-list": TransactionsForReportList,
  "configuration-integration-list": IntegrationList,
  "settlement-list": SettlementList,
  "apps-list": AppList,
  "flow-list": FlowList,
  "flow-listing": FlowListing,
  "circle-company-list": CircleCompanyList,
  "merchant-list": MerchantListing,
  "configuration-mdr-billing-list": MdrBillingList,
  "company-users-list": UserList,
  "company-terms-list": TermsList,
  "notes-activity": NotesActivity,
  "add-note-modal": AddNoteModal,
  "company-terminal-list": withSearch(
    TerminalList,
    "Terminals",
    undefined,
    false
  ),
  "company-directors-list": withSearch(
    DirectorList,
    "Directors",
    (props) => (
      <Button
        href={`/merchants/${props.companyid}/structure/${props.type}/add`}
        size="sm"
      >
        <i className="fas fa-plus" /> Add {props.type}
      </Button>
    ),
    false
  ),
  "company-shareholders-list": withSearch(
    DirectorList,
    "Shareholders",
    (props) => (
      <Button
        href={`/merchants/${props.companyid}/structure/${props.type}/add`}
        size="sm"
      >
        <i className="fas fa-plus" /> Add {props.type}
      </Button>
    ),
    false
  ),
  "company-partners-list": withSearch(
    DirectorList,
    "Partners",
    (props) => (
      <Button
        href={`/merchants/${props.companyid}/structure/${props.type}/add`}
        size="sm"
      >
        <i className="fas fa-plus" /> Add {props.type}
      </Button>
    ),
    false
  ),
  "company-payment-methods-list": PaymentMethodList,
  "company-bank-account-list": BankAccountList,
  "sms-list": withSearch(SmsList, "SMS", undefined, false),
  "push-notification-list": withSearch(
    PushNotificationList,
    "Push Notifications",
    undefined,
    false
  ),
  "email-notification-list": withSearch(
    EmailList,
    "Email Notifications",
    undefined,
    false
  ),
  "company-documents-list": withSearch(
    ComplianceFileList,
    "Compliance Files",
    undefined,
    false
  ),
  "configuration-roles-list": RolesList,
  "log-list": withSearch(EventList, "Event", undefined, false),
  "federated-identity-list": FederatedIdentityList,
  "federated-identity-listing": FederatedIdentityListing,
  "logs-list": LogList,
  "locations-list": LocationsList,
  "products-list": ProductsList,
  "risk-profile-list": RiskProfileList,
  "compliance-case-list": CaseListing,
  "case-transactions-list": CaseTransactionsList,
  "platform-billing-report-list": PlatformBillingReportsList,
  "payout-reports-list": PayoutReportsList,
  "remittance-list": RemittanceListing,
  "remittance-transaction-list": RemittanceTransactionList,
  "onboarding-batch-dynamic-code-list": BatchDynamicCodeList,
  "dynamic-code-list": DynamicCodeList,
  "merchant-balance": BalanceAndRefunds,
  "animated-icon": AnimatedIcon,
  "reports-my-list": MyReportsList,
  "dispute-list": DisputeList,
  "reports-autocredit-list": AutoCreditList,
  "autocredit-list": AutoCreditList,
  "nav-search": NavSearch,
  "onboarding-invitation-list": InvitationList,
  "onboarding-registration-list": RegistrationList,
  "configuration-migrations-list": MigrationsList,
  "company-remove-button": CompanyRemoveButton,
  "user-remove-button": UserRemoveButton,
  "location-list": LocationListing,
  "bank-list": BankList,
  "add-another-form": AddAnotherForm,
  "flow-widget": Flow,
  "merchant-activity-log-list": MerchantActivityLogList,
  "remittance-mark-paid": MarkAsPaidAction,
  "dispute-note-list": DisputeNoteList,
  "holiday-calendar-list": HolidayCalendarList,
  "merchant-requests-list": MerchantRequestListing,
};

Object.entries(webComponents).forEach(([elementName, Component]): void => {
  return registerAsWebComponent(withIntl(Component), elementName, "element");
});
