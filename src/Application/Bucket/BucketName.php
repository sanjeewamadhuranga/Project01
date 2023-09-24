<?php

declare(strict_types=1);

namespace App\Application\Bucket;

enum BucketName
{
    case TRANSACTION;
    case COMPLIANCE;
    case TRANSACTION_REPORTS;
    case MANAGEMENT_REPORTS;
}
