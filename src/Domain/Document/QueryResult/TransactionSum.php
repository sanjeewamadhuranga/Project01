<?php

declare(strict_types=1);

namespace App\Domain\Document\QueryResult;

use App\Application\Stats\StatsResult;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\QueryResultDocument]
class TransactionSum implements StatsResult
{
    #[MongoDB\Field(type: MongoDBType::INT)]
    public int $amount = 0;

    #[MongoDB\Field(name: '_id', type: MongoDBType::DATE)]
    public DateTimeInterface $date;

    public function __construct(DateTimeInterface $date)
    {
        $this->date = $date;
    }
}
