<?php

declare(strict_types=1);

namespace App\Domain\Document\Configuration;

use App\Domain\Document\BaseDocument;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'holiday_calendar')]
class HolidayCalendar extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected DateTimeInterface $date;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $description;

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
