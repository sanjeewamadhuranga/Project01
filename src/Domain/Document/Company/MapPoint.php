<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Document\Traits\HasId;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class MapPoint
{
    use HasId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $latitude;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $longitude;

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): void
    {
        $this->longitude = $longitude;
    }
}
