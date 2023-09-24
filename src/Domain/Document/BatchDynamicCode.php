<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Interfaces\Activeable;
use App\Domain\Document\Traits\HasActive;
use App\Infrastructure\Repository\BatchDynamicCodeRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'batch_dynamic_codes', repositoryClass: BatchDynamicCodeRepository::class)]
class BatchDynamicCode extends BaseDocument implements Activeable
{
    use HasActive;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $title = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $description = null;

    protected int $number = 0;

    public function isTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function getFilenameForExport(): string
    {
        $title = $this->title ?? 'batch_export';

        return str_replace(' ', '_', "{$title}.zip");
    }
}
