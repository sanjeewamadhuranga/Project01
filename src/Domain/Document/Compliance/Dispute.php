<?php

declare(strict_types=1);

namespace App\Domain\Document\Compliance;

use App\Domain\Compliance\DisputeReason;
use App\Domain\Compliance\DisputeState;
use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Transaction\Transaction;
use App\Domain\Transaction\Status;
use App\Infrastructure\Repository\DisputeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Gedmo\Mapping\Annotation as Gedmo;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[MongoDB\Document(collection: 'disputes', repositoryClass: DisputeRepository::class)]
class Dispute extends BaseDocument implements CompanyAware
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $comments = null;

    #[MongoDB\Field(enumType: DisputeState::class)]
    protected ?DisputeState $state = DisputeState::NEW;

    #[MongoDB\Field(enumType: DisputeReason::class)]
    protected ?DisputeReason $reason = null;

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $disputeFee = 0;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Transaction::class)]
    protected ?Transaction $transaction = null;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Transaction::class)]
    protected ?Transaction $chargeback = null;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Transaction::class)]
    protected ?Transaction $reconfirmation = null;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected Company $company;

    #[Gedmo\Blameable(on: 'create')]
    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Administrator::class)]
    protected ?Administrator $handler = null;

    /**
     * @var Collection<int, DisputeNote>
     */
    #[MongoDB\EmbedMany(targetDocument: DisputeNote::class)]
    protected Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public static function forTransaction(Transaction $transaction): self
    {
        if (null === $transaction->getMerchant()) {
            throw new InvalidArgumentException(sprintf('Transaction: %s does not have a merchant.', $transaction->getId()));
        }
        $dispute = new self();
        $dispute->transaction = $transaction;
        $dispute->company = $transaction->getMerchant();

        return $dispute;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    public function getState(): ?DisputeState
    {
        return $this->state;
    }

    public function setState(?DisputeState $state): void
    {
        $this->state = $state;
    }

    public function getReason(): ?DisputeReason
    {
        return $this->reason;
    }

    public function setReason(?DisputeReason $reason): void
    {
        $this->reason = $reason;
    }

    public function getDisputeFee(): int
    {
        return $this->disputeFee;
    }

    public function setDisputeFee(int $disputeFee): void
    {
        $this->disputeFee = $disputeFee;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transactions): void
    {
        $this->transaction = $transactions;
    }

    public function getChargeback(): ?Transaction
    {
        return $this->chargeback;
    }

    public function setChargeback(?Transaction $chargeback): void
    {
        $this->chargeback = $chargeback;
    }

    public function getReconfirmation(): ?Transaction
    {
        return $this->reconfirmation;
    }

    public function setReconfirmation(?Transaction $reconfirmation): void
    {
        $this->reconfirmation = $reconfirmation;
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

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (!in_array($this->getTransaction()?->getState(), [
            Status::REFUND_REQUESTED,
            Status::REFUNDED,
            Status::CONFIRMED, ], true)
        ) {
            $context->buildViolation('Invalid state for dispute')
                ->atPath('transaction')
                ->addViolation()
            ;
        }
    }

    public function proceedReconfirmation(Transaction $reconfirmation): void
    {
        $this->transaction?->setAvailableBalance($reconfirmation->getAmount());
        $this->reconfirmation = $reconfirmation;
        $this->close();
    }

    public function proceedChargeback(Transaction $chargeback): void
    {
        $this->transaction?->setAvailableBalance(0);
        $chargeback->setIsDispute(true);
        $chargeback->setParentTransaction($this->getTransaction());
        $this->setChargeback($chargeback);
        $this->setState(DisputeState::PROCESSING);
    }

    public function close(): void
    {
        $this->state = DisputeState::CLOSED;
    }

    public function isMutable(): bool
    {
        return in_array($this->getState(), [DisputeState::NEW, DisputeState::PROCESSING], true);
    }

    public function canCreateReconfirmation(): bool
    {
        return $this->isMutable()
            && $this->hasChargeback()
            && $this->hasTransaction();
    }

    public function canCreateChargeback(): bool
    {
        return $this->isMutable()
            && !$this->hasChargeback()
            && $this->hasTransaction();
    }

    public function hasChargeback(): bool
    {
        return null !== $this->getChargeback();
    }

    public function hasTransaction(): bool
    {
        return null !== $this->getTransaction();
    }

    /**
     * @return Collection<int, DisputeNote>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    /**
     * @param Collection<int, DisputeNote> $notes
     */
    public function setNotes(Collection $notes): void
    {
        $this->notes = $notes;
    }

    public function addNote(DisputeNote $disputeNote): void
    {
        $this->notes[] = $disputeNote;
    }

    /**
     * @return Collection<int, DisputeNote>
     */
    public function getActiveNotes(): Collection
    {
        return $this->getNotes()->filter(fn (DisputeNote $disputeNote) => !$disputeNote->isDeleted());
    }

    public function getNote(string $id): ?DisputeNote
    {
        $note = $this->getNotes()
            ->filter(static fn (DisputeNote $note) => $note->getId() === $id)
            ->first();

        return false !== $note ? $note : null;
    }
}
