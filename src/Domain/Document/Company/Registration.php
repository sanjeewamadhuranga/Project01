<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Invitation;
use App\Infrastructure\Repository\Company\RegistrationRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'registration_pending', repositoryClass: RegistrationRepository::class)]
#[MongoDB\HasLifecycleCallbacks]
class Registration extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $sub;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $phoneNumber;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $completed = false;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $currentSection = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $currentScreen = null;

    #[MongoDB\Field(type: MongoDBType::RAW)]
    protected mixed $flow = null;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Invitation::class)]
    protected ?Invitation $invitation = null;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected ?Company $company = null;

    public function getSub(): string
    {
        return $this->sub;
    }

    public function setSub(string $sub): void
    {
        $this->sub = $sub;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getCurrentSection(): ?string
    {
        return $this->currentSection;
    }

    public function setCurrentSection(string $currentSection): void
    {
        $this->currentSection = $currentSection;
    }

    public function getCurrentScreen(): ?string
    {
        return $this->currentScreen;
    }

    public function setCurrentScreen(string $currentScreen): void
    {
        $this->currentScreen = $currentScreen;
    }

    public function getFlow(): mixed
    {
        return $this->flow;
    }

    public function setFlow(mixed $flow): void
    {
        $this->flow = $flow;
    }

    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    public function setInvitation(Invitation $invitation): void
    {
        $this->invitation = $invitation;
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
