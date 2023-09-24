<?php

declare(strict_types=1);

namespace App\Domain\Document\Addons;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Traits\HasImages;
use App\Infrastructure\Repository\BetaFeatureRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[MongoDB\Document(collection: 'addons_betafeatures', repositoryClass: BetaFeatureRepository::class)]
class BetaFeature extends BaseDocument
{
    use HasImages;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $code;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $title;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $description;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
