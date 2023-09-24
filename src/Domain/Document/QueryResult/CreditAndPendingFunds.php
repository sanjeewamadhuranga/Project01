<?php

declare(strict_types=1);

namespace App\Domain\Document\QueryResult;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\QueryResultDocument]
class CreditAndPendingFunds
{
    #[MongoDB\Field(type: MongoDBType::INT)]
    public int $credit = 0;

    #[MongoDB\Field(type: MongoDBType::INT)]
    public int $pendingFunds = 0;

    public function getBalance(): int
    {
        return $this->credit - $this->pendingFunds;
    }
}
