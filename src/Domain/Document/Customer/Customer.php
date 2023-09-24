<?php

declare(strict_types=1);

namespace App\Domain\Document\Customer;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Infrastructure\Repository\Customer\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: 'customers', repositoryClass: CustomerRepository::class, readOnly: true)]
class Customer extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $name = null;

    #[Assert\Email]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $email = null;

    #[Assert\Email]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $billingEmail = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $billingAddress1 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $billingAddress2 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $billingCity = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $billingCountry = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $billingPostCode = null;

    #[MongoDB\ReferenceOne(name: 'customerGroupId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: CustomerGroup::class)]
    protected ?CustomerGroup $customerGroup = null;

    #[MongoDB\ReferenceOne(name: 'companyId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected ?Company $company = null;

    #[MongoDB\EmbedOne(targetDocument: User::class)]
    protected ?User $addedBy = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $currency = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $paymentDue = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $balanceDue = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $invoicePrefix = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $taxInformation = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $taxId = null;

    /**
     * @var Collection<int, CustomerTransaction>
     */
    #[MongoDB\EmbedMany(targetDocument: CustomerTransaction::class)]
    protected Collection $customerPayments;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $baseBalanceDue = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $lastReconciledAt = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $lastTransactionId = null;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $idempotencyKey = [];

    #[MongoDB\EmbedOne(targetDocument: AccountManager::class)]
    protected ?AccountManager $accountManager = null;

    public function __construct()
    {
        $this->customerPayments = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getBillingEmail(): ?string
    {
        return $this->billingEmail;
    }

    public function setBillingEmail(?string $billingEmail): void
    {
        $this->billingEmail = $billingEmail;
    }

    public function getBillingAddress1(): ?string
    {
        return $this->billingAddress1;
    }

    public function setBillingAddress1(?string $billingAddress1): void
    {
        $this->billingAddress1 = $billingAddress1;
    }

    public function getBillingAddress2(): ?string
    {
        return $this->billingAddress2;
    }

    public function setBillingAddress2(?string $billingAddress2): void
    {
        $this->billingAddress2 = $billingAddress2;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(?string $billingCity): void
    {
        $this->billingCity = $billingCity;
    }

    public function getBillingCountry(): ?string
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(?string $billingCountry): void
    {
        $this->billingCountry = $billingCountry;
    }

    public function getBillingPostCode(): ?string
    {
        return $this->billingPostCode;
    }

    public function setBillingPostCode(?string $billingPostCode): void
    {
        $this->billingPostCode = $billingPostCode;
    }

    public function getCustomerGroup(): ?CustomerGroup
    {
        return $this->customerGroup;
    }

    public function setCustomerGroup(?CustomerGroup $customerGroup): void
    {
        $this->customerGroup = $customerGroup;
    }

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(?User $addedBy): void
    {
        $this->addedBy = $addedBy;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getPaymentDue(): ?int
    {
        return $this->paymentDue;
    }

    public function setPaymentDue(?int $paymentDue): void
    {
        $this->paymentDue = $paymentDue;
    }

    public function getBalanceDue(): ?int
    {
        return $this->balanceDue;
    }

    public function setBalanceDue(?int $balanceDue): void
    {
        $this->balanceDue = $balanceDue;
    }

    public function getInvoicePrefix(): ?string
    {
        return $this->invoicePrefix;
    }

    public function setInvoicePrefix(?string $invoicePrefix): void
    {
        $this->invoicePrefix = $invoicePrefix;
    }

    public function getTaxInformation(): ?string
    {
        return $this->taxInformation;
    }

    public function setTaxInformation(?string $taxInformation): void
    {
        $this->taxInformation = $taxInformation;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): void
    {
        $this->taxId = $taxId;
    }

    /**
     * @return Collection<int, CustomerTransaction>
     */
    public function getCustomerPayments(): Collection
    {
        return $this->customerPayments;
    }

    /**
     * @param Collection<int, CustomerTransaction> $customerPayments
     */
    public function setCustomerPayments(Collection $customerPayments): void
    {
        $this->customerPayments = $customerPayments;
    }

    public function getBaseBalanceDue(): ?int
    {
        return $this->baseBalanceDue;
    }

    public function setBaseBalanceDue(?int $baseBalanceDue): void
    {
        $this->baseBalanceDue = $baseBalanceDue;
    }

    public function getLastReconciledAt(): ?string
    {
        return $this->lastReconciledAt;
    }

    public function setLastReconciledAt(?string $lastReconciledAt): void
    {
        $this->lastReconciledAt = $lastReconciledAt;
    }

    public function getLastTransactionId(): ?string
    {
        return $this->lastTransactionId;
    }

    public function setLastTransactionId(?string $lastTransactionId): void
    {
        $this->lastTransactionId = $lastTransactionId;
    }

    /**
     * @return string[]
     */
    public function getIdempotencyKey(): array
    {
        return $this->idempotencyKey;
    }

    /**
     * @param string[] $idempotencyKey
     */
    public function setIdempotencyKey(array $idempotencyKey): void
    {
        $this->idempotencyKey = $idempotencyKey;
    }

    public function getAccountManager(): ?AccountManager
    {
        return $this->accountManager;
    }

    public function setAccountManager(?AccountManager $accountManager): void
    {
        $this->accountManager = $accountManager;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }
}
