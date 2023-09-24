<?php

declare(strict_types=1);

namespace App\Domain\Document\QueryResult;

use App\Application\Stats\StatsResult;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\QueryResultDocument]
class TransactionTotal implements StatsResult
{
    #[MongoDB\Field(name: '_id', type: MongoDBType::STRING)]
    public string $currency;

    #[MongoDB\Field(type: MongoDBType::INT)]
    public int $amount = 0;

    #[MongoDB\Field(type: MongoDBType::INT)]
    public int $count = 0;

    #[MongoDB\Field(type: MongoDBType::INT)]
    public int $avg = 0;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    public DateTimeInterface $start;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    public DateTimeInterface $end;

    public function __construct(string $currency)
    {
        $this->currency = $currency;
    }
}
