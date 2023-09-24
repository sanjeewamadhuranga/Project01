<?php

declare(strict_types=1);

namespace App\Domain\ActivityLog;

interface ActivityLogType
{
    final public const MERCHANT_REVIEW_STATUS_EDIT = 'merchant.review_status.edit';
    final public const MERCHANT_REVIEW_STATUS_CONFIRM = 'merchant.review_status.edit.confirm';
    final public const MERCHANT_SUBSCRIPTION_PLAN_EDIT = 'merchant.subscription.edit';
    final public const MERCHANT_USER_ADD = 'merchant.user.add';
    final public const MERCHANT_USER_EDIT = 'merchant.user.edit';
    final public const MERCHANT_DELETED = 'merchant.deleted';
    final public const MERCHANT_USER_COMPLIANCE_EDIT = 'merchant.user.edit';
    final public const MERCHANT_USER_DELETE = 'merchant.user.delete';
    final public const MERCHANT_USER_DELETE_CONFIRM = 'merchant.user.delete.confirm';
    final public const MERCHANT_USER_ADD_CONFIRM = 'merchant.user.add.confirm';
    final public const MERCHANT_USER_EDIT_CONFIRM = 'merchant.user.edit.confirm';
    final public const MERCHANT_METADATA_EDIT = 'merchant.metadata.edit';
    final public const MERCHANT_METADATA_EDIT_CONFIRM = 'merchant.metadata.edit.confirm';
    final public const ADMINISTRATOR_REPORT_VIEW = 'administrator.report.view';
    final public const MERCHANT_BANK_ACCOUNTS_EDIT = 'merchant.bank_accounts.edit';
    final public const ADMINISTRATOR_REPORT_DOWNLOAD = 'administrator.report.download';
    final public const AUTHENTICATION_FAILURE = 'administrator.authentication.failure';
    final public const AUTHENTICATION_2FA_FAILURE = 'administrator.authentication.2fa.failure';
    final public const AUTHENTICATION_SUCCESS = 'administrator.authentication.success';
    final public const TWO_FACTOR_SETUP_APP = 'administrator.2fa.setup.app';
    final public const TWO_FACTOR_SETUP_SMS = 'administrator.2fa.setup.sms';
    final public const TWO_FACTOR_DISABLE_APP = 'administrator.2fa.disable.app';
    final public const TWO_FACTOR_DISABLE_SMS = 'administrator.2fa.disable.sms';
    final public const COMMIT_UPDATE = 'document.update';
    final public const COMMIT_CREATE = 'document.create';
}
