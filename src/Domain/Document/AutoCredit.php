<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Application\Bucket\BucketName;
use App\Infrastructure\Repository\AutoCreditRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'autocredits', repositoryClass: AutoCreditRepository::class, readOnly: true)]
class AutoCredit extends BaseDocument implements ReportInterface
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $headerRecordType;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $headerBranchId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $companyName;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $processingDate;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $footerRecordType;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $footerBranchId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $totalAmount;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $processed;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $autocreditItemIds;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $filename;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $ownBank;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $processedByReseller;

    public function getHeaderRecordType(): string
    {
        return $this->headerRecordType;
    }

    public function setHeaderRecordType(string $headerRecordType): void
    {
        $this->headerRecordType = $headerRecordType;
    }

    public function getHeaderBranchId(): string
    {
        return $this->headerBranchId;
    }

    public function setHeaderBranchId(string $headerBranchId): void
    {
        $this->headerBranchId = $headerBranchId;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getProcessingDate(): string
    {
        return $this->processingDate;
    }

    public function setProcessingDate(string $processingDate): void
    {
        $this->processingDate = $processingDate;
    }

    public function getFooterRecordType(): string
    {
        return $this->footerRecordType;
    }

    public function setFooterRecordType(string $footerRecordType): void
    {
        $this->footerRecordType = $footerRecordType;
    }

    public function getFooterBranchId(): string
    {
        return $this->footerBranchId;
    }

    public function setFooterBranchId(string $footerBranchId): void
    {
        $this->footerBranchId = $footerBranchId;
    }

    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getProcessed(): string
    {
        return $this->processed;
    }

    public function setProcessed(string $processed): void
    {
        $this->processed = $processed;
    }

    /**
     * @return string[]
     */
    public function getAutocreditItemIds(): array
    {
        return $this->autocreditItemIds;
    }

    /**
     * @param string[] $autocreditItemIds
     */
    public function setAutocreditItemIds(array $autocreditItemIds): void
    {
        $this->autocreditItemIds = $autocreditItemIds;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function isOwnBank(): bool
    {
        return $this->ownBank;
    }

    public function setOwnBank(bool $ownBank): void
    {
        $this->ownBank = $ownBank;
    }

    public function isProcessedByReseller(): bool
    {
        return $this->processedByReseller;
    }

    public function setProcessedByReseller(bool $processedByReseller): void
    {
        $this->processedByReseller = $processedByReseller;
    }

    public function getBucketName(): BucketName
    {
        return BucketName::TRANSACTION_REPORTS;
    }
}
