<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Domain\Document\Provider\Provider;
use App\Infrastructure\Repository\Company\ProviderOnboardingRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'provider_onboardings', repositoryClass: ProviderOnboardingRepository::class)]
class ProviderOnboarding extends BaseDocument implements CompanyAware
{
    /**
     * @var Collection<int, ProviderOnboardingAttempt>
     */
    #[MongoDB\EmbedMany(targetDocument: ProviderOnboardingAttempt::class)]
    protected Collection $attempts;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $dateEnabled = null;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $dateDisabled = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $status;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Provider::class)]
    protected Provider $provider;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $providerKey;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected Company $company;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $unavailableReason = null;

    public function __construct()
    {
        $this->attempts = new ArrayCollection();
    }

    /**
     * @return Collection<int, ProviderOnboardingAttempt>
     */
    public function getAttempts(): Collection
    {
        return $this->attempts;
    }

    /**
     * @param Collection<int, ProviderOnboardingAttempt> $attempts
     */
    public function setAttempts(Collection $attempts): void
    {
        $this->attempts = $attempts;
    }

    public function getDateEnabled(): ?DateTimeInterface
    {
        return $this->dateEnabled;
    }

    public function setDateEnabled(?DateTimeInterface $dateEnabled): void
    {
        $this->dateEnabled = $dateEnabled;
    }

    public function getDateDisabled(): ?DateTimeInterface
    {
        return $this->dateDisabled;
    }

    public function setDateDisabled(?DateTimeInterface $dateDisabled): void
    {
        $this->dateDisabled = $dateDisabled;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }

    public function getProviderKey(): string
    {
        return $this->providerKey;
    }

    public function setProviderKey(string $providerKey): void
    {
        $this->providerKey = $providerKey;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function addAttempt(ProviderOnboardingAttempt $attempt): void
    {
        $this->attempts->add($attempt);
    }

    public function getUnavailableReason(): ?string
    {
        return $this->unavailableReason;
    }

    public function setUnavailableReason(?string $unavailableReason): void
    {
        $this->unavailableReason = $unavailableReason;
    }
}
