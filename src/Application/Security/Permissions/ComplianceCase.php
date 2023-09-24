<?php

declare(strict_types=1);

namespace App\Application\Security\Permissions;

use App\Domain\Settings\ExposesConsts;

final class ComplianceCase
{
    use ExposesConsts;

    final public const ASSUME_REVIEW = 'COMPLIANCE_CASE_ASSUME_REVIEW';
    final public const ASSUME_APPROVE = 'COMPLIANCE_CASE_ASSUME_APPROVE';
    final public const ASSIGN_REVIEW = 'COMPLIANCE_CASE_ASSIGN_REVIEW';
    final public const ASSIGN_APPROVE = 'COMPLIANCE_CASE_ASSIGN_APPROVE';
    final public const REVIEW = 'COMPLIANCE_CASE_REVIEW';
    final public const APPROVE = 'COMPLIANCE_CASE_APPROVE';
}
