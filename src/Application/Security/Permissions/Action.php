<?php

declare(strict_types=1);

namespace App\Application\Security\Permissions;

final class Action
{
    final public const VIEW = 'view';
    final public const CREATE = 'create';
    final public const EDIT = 'edit';
    final public const DELETE = 'delete';
    final public const ASSIGN = 'assign';
    final public const APPROVE = 'approve';
    final public const REVIEW = 'review';
    final public const DOWNLOAD = 'download';
    final public const LOCK = 'lock';
    final public const ENABLE = 'enable';
    final public const DISABLE = 'disable';
    final public const COMPLETE = 'complete';
    final public const ENABLE_2FA = '2fa_enable';
    final public const REQUEST_REFUND = 'request_refund';
    final public const RETRY_SECONDARY_ACQUIRING = 'retry_secondary_acquiring';
    final public const ANY = '*';

    /**
     * Contains all valid options in one key.
     */
    final public const CRUD = [
        self::VIEW,
        self::CREATE,
        self::EDIT,
        self::DELETE,
        self::ANY,
    ];
}
