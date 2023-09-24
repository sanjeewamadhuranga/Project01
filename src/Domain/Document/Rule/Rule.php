<?php

declare(strict_types=1);

namespace App\Domain\Document\Rule;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Interfaces\Activeable;
use App\Domain\Document\Traits\HasActive;
use App\Domain\Rule\DecisionType;
use App\Domain\Rule\EventType;
use App\Infrastructure\Repository\RuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'rules', repositoryClass: RuleRepository::class)]
class Rule extends BaseDocument implements Activeable
{
    use HasActive;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $name = null;

    #[MongoDB\Field(enumType: DecisionType::class)]
    protected ?DecisionType $decision = null;

    #[MongoDB\Field(enumType: EventType::class)]
    protected ?EventType $event = null;

    /**
     * @var Collection<int, Restriction>
     */
    #[MongoDB\EmbedMany(targetDocument: Restriction::class)]
    protected Collection $restrictions;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $decisionData = null;

    public function __construct()
    {
        $this->restrictions = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDecision(): ?DecisionType
    {
        return $this->decision;
    }

    public function setDecision(?DecisionType $decision): void
    {
        $this->decision = $decision;
    }

    public function getEvent(): ?EventType
    {
        return $this->event;
    }

    public function setEvent(?EventType $event): void
    {
        $this->event = $event;
    }

    /**
     * @return Collection<int, Restriction>
     */
    public function getRestrictions(): Collection
    {
        return $this->restrictions;
    }

    /**
     * @param Collection<int, Restriction> $restrictions
     */
    public function setRestrictions(Collection $restrictions): void
    {
        $this->restrictions = $restrictions;
    }

    public function getDecisionData(): ?string
    {
        return $this->decisionData;
    }

    public function setDecisionData(?string $decisionData): void
    {
        $this->decisionData = $decisionData;
    }
}
