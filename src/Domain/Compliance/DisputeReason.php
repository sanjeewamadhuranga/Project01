<?php

declare(strict_types=1);

namespace App\Domain\Compliance;

enum DisputeReason: string
{
    case FRAUD = 'compliance.dispute.fraud';
    case CREDIT_NOT_RECEIVED = 'compliance.dispute.credit_not_received';
    case PRODUCT_UNACCEPTABLE = 'compliance.dispute.product_unacceptable';
    case CHARGE_NOT_RECOGNIZED = 'compliance.dispute.charge_not_recognized';
    case INQUIRY = 'compliance.dispute.inquiry';
    case PRODUCT_NOT_RECEIVED = 'compliance.dispute.product_not_received';
}
