<?php

declare(strict_types=1);

namespace App\Domain\Document\Compliance;

use App\Domain\Compliance\PayoutBlockReason;
use App\Domain\Compliance\PayoutBlockStatus;
use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Transaction\Transaction;
use App\Infrastructure\Repository\PayoutBlockRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: 'compliance_payoutblocks', repositoryClass: PayoutBlockRepository::class)]
class PayoutBlock extends BaseDocument implements CompanyAware
{
    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $reviewed = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $approved = false;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $comments = null;

    #[MongoDB\Field(enumType: PayoutBlockReason::class)]
    protected ?PayoutBlockReason $reason = null;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $ignoreDate = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Transaction::class)]
    protected Collection $transactions;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected Company $company;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Administrator::class)]
    #[Assert\NotEqualTo(propertyPath: 'approver')]
    protected ?Administrator $handler = null;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Administrator::class)]
    #[Assert\NotEqualTo(propertyPath: 'handler')]
    protected ?Administrator $approver = null;

    #[MongoDB\EmbedOne(targetDocument: CaseFlow::class)]
    protected ?CaseFlow $caseFlow = null;

    public function getStatus(): PayoutBlockStatus
    {
        if (true === $this->isApproved()
            && true === $this->isReviewed()
        ) {
            return PayoutBlockStatus::CLOSED;
        }

        if (false === $this->isReviewed()
            && null !== $this->getHandler()
        ) {
            return PayoutBlockStatus::IN_REVIEW;
        }

        if (true === $this->isReviewed()
            && false === $this->isApproved()
        ) {
            return PayoutBlockStatus::IN_APPROVAL;
        }

        return PayoutBlockStatus::OPEN;
    }

    public function getEmail(): ?string
    {
        if (PayoutBlockStatus::IN_REVIEW === $this->getStatus()) {
            return $this->getHandler()?->getEmail();
        }

        if (PayoutBlockStatus::IN_APPROVAL === $this->getStatus()) {
            return $this->getApprover()?->getEmail();
        }

        return null;
    }

    public function isReviewed(): bool
    {
        return $this->reviewed;
    }

    public function setReviewed(bool $reviewed): void
    {
        $this->reviewed = $reviewed;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): void
    {
        $this->approved = $approved;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    public function getReason(): ?PayoutBlockReason
    {
        return $this->reason;
    }

    public function setReason(?PayoutBlockReason $reason): void
    {
        $this->reason = $reason;
    }

    public function getIgnoreDate(): ?DateTimeInterface
    {
        return $this->ignoreDate;
    }

    public function setIgnoreDate(?DateTimeInterface $ignoreDate): void
    {
        $this->ignoreDate = $ignoreDate;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * @param Collection<int, Transaction> $transactions
     */
    public function setTransactions(Collection $transactions): void
    {
        $this->transactions = $transactions;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getHandler(): ?Administrator
    {
        return $this->handler;
    }

    public function setHandler(?Administrator $handler): void
    {
        $this->handler = $handler;
    }

    public function getApprover(): ?Administrator
    {
        return $this->approver;
    }

    public function setApprover(?Administrator $approver): void
    {
        $this->approver = $approver;
    }

    public function getCaseFlow(): ?CaseFlow
    {
        return $this->caseFlow;
    }

    public function setCaseFlow(CaseFlow $caseFlow): void
    {
        $this->caseFlow = $caseFlow;
    }
}
