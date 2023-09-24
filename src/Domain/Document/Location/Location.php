<?php

declare(strict_types=1);

namespace App\Domain\Document\Location;

use App\Application\DTO\Choiceable;
use App\Domain\Company\LocationStatus;
use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\DiscountCode;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Infrastructure\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;

#[MongoDB\Document(collection: 'locations', repositoryClass: LocationRepository::class)]
class Location extends BaseDocument implements Stringable, CompanyAware, Choiceable
{
    #[MongoDB\ReferenceOne(name: 'companyId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected Company $company;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $name = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $reference = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $address1 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $address2 = null;

    #[MongoDB\EmbedOne(targetDocument: QR::class)]
    protected ?QR $qr = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $postalCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $city = null;

    #[MongoDB\Field(enumType: LocationStatus::class)]
    protected LocationStatus $status = LocationStatus::OPEN;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $alipayOnboarded = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $alipayOnlineOnboarded = false;

    #[MongoDB\ReferenceOne(name: 'discountCode', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: DiscountCode::class)]
    protected ?DiscountCode $discountCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $customDomain = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $customDomainValidated = null;

    /**
     * @var Collection<int, Onboarding>
     */
    #[MongoDB\EmbedMany(targetDocument: Onboarding::class)]
    protected Collection $onboarding;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::RAW)]
    protected ?array $publishedChannels = null;

    public function __construct()
    {
        $this->onboarding = new ArrayCollection();
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(string $address1): void
    {
        $this->address1 = $address1;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(string $address2): void
    {
        $this->address2 = $address2;
    }

    public function getQr(): ?QR
    {
        return $this->qr;
    }

    public function setQr(?QR $qr): void
    {
        $this->qr = $qr;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getStatus(): LocationStatus
    {
        return $this->status;
    }

    public function setStatus(LocationStatus $status): void
    {
        $this->status = $status;
    }

    public function isAlipayOnboarded(): bool
    {
        return $this->alipayOnboarded;
    }

    public function setAlipayOnboarded(bool $alipayOnboarded): void
    {
        $this->alipayOnboarded = $alipayOnboarded;
    }

    public function isAlipayOnlineOnboarded(): bool
    {
        return $this->alipayOnlineOnboarded;
    }

    public function setAlipayOnlineOnboarded(bool $alipayOnlineOnboarded): void
    {
        $this->alipayOnlineOnboarded = $alipayOnlineOnboarded;
    }

    public function isDiscountCode(): ?DiscountCode
    {
        return $this->discountCode;
    }

    public function setDiscountCode(?DiscountCode $discountCode): void
    {
        $this->discountCode = $discountCode;
    }

    public function getCustomDomain(): ?string
    {
        return $this->customDomain;
    }

    public function setCustomDomain(?string $customDomain): void
    {
        $this->customDomain = $customDomain;
    }

    public function isCustomDomainValidated(): ?bool
    {
        return $this->customDomainValidated;
    }

    public function setCustomDomainValidated(?bool $customDomainValidated): void
    {
        $this->customDomainValidated = $customDomainValidated;
    }

    public function __toString()
    {
        return $this->name ?? $this->id ?? '';
    }

    /**
     * @return Collection<int, Onboarding>
     */
    public function getOnboarding(): Collection
    {
        return $this->onboarding;
    }

    /**
     * @param Collection<int, Onboarding> $onboarding
     */
    public function setOnboarding(Collection $onboarding): void
    {
        $this->onboarding = $onboarding;
    }

    /**
     * @return string[]
     */
    public function getPublishedChannels(): ?array
    {
        return $this->publishedChannels;
    }

    /**
     * @param string[] $publishedChannels
     */
    public function setPublishedChannels(?array $publishedChannels): void
    {
        $this->publishedChannels = $publishedChannels;
    }

    public function getChoiceName(): string
    {
        return (string) $this->getName();
    }
}
