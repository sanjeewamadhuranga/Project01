<?php

declare(strict_types=1);

namespace App\Domain\Document\Customer;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Transaction\Transaction;
use App\Domain\Transaction\Status;
use App\Infrastructure\Repository\Customer\CustomerTransactionLogRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'customer_transaction_logs', repositoryClass: CustomerTransactionLogRepository::class, readOnly: true)]
class CustomerTransactionLog extends BaseDocument
{
    #[MongoDB\ReferenceOne(name: 'transactionId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Transaction::class)]
    protected ?Transaction $transaction = null;

    #[MongoDB\ReferenceOne(name: 'customerId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Customer::class)]
    protected ?Customer $customer = null;

    #[MongoDB\ReferenceOne(name: 'companyId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected ?Company $company = null;

    #[MongoDB\Field(type: MongoDBType::OBJECTID)]
    protected ?string $invoiceId = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $amount = null;

    #[MongoDB\Field(enumType: Status::class)]
    protected ?Status $state = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $currency = null;

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getInvoiceId(): ?string
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(?string $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getState(): ?Status
    {
        return $this->state;
    }

    public function setState(?Status $state): void
    {
        $this->state = $state;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }
}
