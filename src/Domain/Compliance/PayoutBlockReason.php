<?php

declare(strict_types=1);

namespace App\Domain\Compliance;

enum PayoutBlockReason: string
{
    case AMOUNT_DAILY = 'compliance.amount.daily';
    case AMOUNT_WEEKLY = 'compliance.amount.weekly';
    case AMOUNT_MONTHLY = 'compliance.amount.monthly';
    case AMOUNT_DUPLICATE = 'compliance.amount.duplicate';
    case BUYER_ID_DUPLICATE = 'compliance.buyer.duplicate';
    case TRANSACTION_COUNT = 'compliance.transaction.count';
    case DATETIME_TRANSACTION = 'compliance.datetime.single.transaction';
    case AMOUNT_SINGLE_TRANSACTION = 'compliance.amount.single.transaction';
}
