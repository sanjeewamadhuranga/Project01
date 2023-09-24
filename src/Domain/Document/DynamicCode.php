<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Location\Location;
use App\Infrastructure\Repository\DynamicCodeRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: 'dynamic_codes', repositoryClass: DynamicCodeRepository::class)]
class DynamicCode extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    #[MongoDB\Index(unique: true)]
    protected ?string $code = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $hits = 0;

    #[Assert\NotBlank]
    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected ?Company $company = null;

    #[Assert\NotBlank]
    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Location::class)]
    protected ?Location $location = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $targetUrl = null;

    #[MongoDB\ReferenceOne(name: 'batchDynamicCodeId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: BatchDynamicCode::class)]
    protected BatchDynamicCode $batch;

    public function __construct(BatchDynamicCode $batch)
    {
        $this->batch = $batch;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getHits(): int
    {
        return $this->hits;
    }

    public function setHits(int $hits): void
    {
        $this->hits = $hits;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    public function getTargetUrl(): ?string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $targetUrl): void
    {
        $this->targetUrl = $targetUrl;
    }

    public function getBatch(): BatchDynamicCode
    {
        return $this->batch;
    }

    public function setBatch(BatchDynamicCode $batch): void
    {
        $this->batch = $batch;
    }

    public function getFilenameForExport(): string
    {
        $code = $this->code ?? 'qr_code';

        return "{$code}.png";
    }
}
