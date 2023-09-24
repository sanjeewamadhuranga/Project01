<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\Repository\AutoCreditItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'autocredits_items', repositoryClass: AutoCreditItemRepository::class)]
class AutoCreditItem extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $recordType;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $branchId;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $bankCode;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $bankName;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $bankBranch;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $accountNumber;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $debitCreditCode;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $transactionAmount;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $currencyCode;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $accountName;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $remarks;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $referenceNumber;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $terminalNumber;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $batchReferenceNumber;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $autocredit;

    /**
     * @var Collection<int, Transaction>
     */
    #[MongoDB\EmbedMany(targetDocument: Transaction::class)]
    protected Collection $transactionIds;

    public function getRecordType(): string
    {
        return $this->recordType;
    }

    public function setRecordType(string $recordType): void
    {
        $this->recordType = $recordType;
    }

    public function getBranchId(): string
    {
        return $this->branchId;
    }

    public function setBranchId(string $branchId): void
    {
        $this->branchId = $branchId;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): void
    {
        $this->bankCode = $bankCode;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): void
    {
        $this->bankName = $bankName;
    }

    public function getBankBranch(): string
    {
        return $this->bankBranch;
    }

    public function setBankBranch(string $bankBranch): void
    {
        $this->bankBranch = $bankBranch;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    public function getDebitCreditCode(): string
    {
        return $this->debitCreditCode;
    }

    public function setDebitCreditCode(string $debitCreditCode): void
    {
        $this->debitCreditCode = $debitCreditCode;
    }

    public function getTransactionAmount(): string
    {
        return $this->transactionAmount;
    }

    public function setTransactionAmount(string $transactionAmount): void
    {
        $this->transactionAmount = $transactionAmount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): void
    {
        $this->accountName = $accountName;
    }

    public function getRemarks(): string
    {
        return $this->remarks;
    }

    public function setRemarks(string $remarks): void
    {
        $this->remarks = $remarks;
    }

    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): void
    {
        $this->referenceNumber = $referenceNumber;
    }

    public function getTerminalNumber(): string
    {
        return $this->terminalNumber;
    }

    public function setTerminalNumber(string $terminalNumber): void
    {
        $this->terminalNumber = $terminalNumber;
    }

    public function getBatchReferenceNumber(): string
    {
        return $this->batchReferenceNumber;
    }

    public function setBatchReferenceNumber(string $batchReferenceNumber): void
    {
        $this->batchReferenceNumber = $batchReferenceNumber;
    }

    public function getAutocredit(): string
    {
        return $this->autocredit;
    }

    public function setAutocredit(string $autocredit): void
    {
        $this->autocredit = $autocredit;
    }

    /**
     * @return Collection<int,Transaction>
     */
    public function getTransactionIds(): Collection
    {
        return $this->transactionIds;
    }

    /**
     * @param Collection<int, Transaction> $transactionIds
     */
    public function setTransactionIds(Collection $transactionIds): void
    {
        $this->transactionIds = $transactionIds;
    }
}
