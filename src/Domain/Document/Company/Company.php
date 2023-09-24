<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Application\DTO\Choiceable;
use App\Domain\Collection\BankAccountCollection;
use App\Domain\Company\CreationSource;
use App\Domain\Company\ReviewStatus;
use App\Domain\Company\RiskLevel;
use App\Domain\Company\Status;
use App\Domain\Company\Type;
use App\Domain\Document\App;
use App\Domain\Document\BaseDocument;
use App\Domain\Document\Compliance\FraudSignal;
use App\Domain\Document\Compliance\PayoutBlock;
use App\Domain\Document\Compliance\RiskProfile;
use App\Domain\Document\Compliance\Whitelist;
use App\Domain\Document\ComplianceFile;
use App\Domain\Document\Invitation;
use App\Domain\Document\Location\Location;
use App\Domain\Document\Provider\Provider;
use App\Domain\Document\Role\Role;
use App\Domain\Document\Term\CompanyTerm;
use App\Domain\Document\Traits\Blacklistable;
use App\Infrastructure\Repository\Company\CompanyRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: 'companies', repositoryClass: CompanyRepository::class)]
class Company extends BaseDocument implements Stringable, Choiceable
{
    use Blacklistable;

    #[MongoDB\Field(enumType: Type::class)]
    protected ?Type $companyType = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $messageType = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $companyLegalType = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $registeredName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $tradingName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $companyNumber = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $vatNumber = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $address1 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $address2 = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $googlePlacesAddress = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $phone = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $businessWebsite = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $timezone = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $language = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    #[Assert\Regex('/^\d{5}$/', message: 'address.postCode', groups: ['dialog'])]
    protected ?string $postalCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $currency = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $state = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $city = null;

    #[MongoDB\Field(enumType: RiskLevel::class)]
    protected ?RiskLevel $riskLevel = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $eventPostUrl = null;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $lastReviewDate = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $reviewReference = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $country = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $coupon = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $merchantCategoryCode = null;

    #[MongoDB\EmbedMany(targetDocument: BankAccount::class, collectionClass: BankAccountCollection::class)]
    protected BankAccountCollection $bankAccounts;

    /**
     * @var Collection<int, AccountManagerNote>
     */
    #[MongoDB\EmbedMany(targetDocument: AccountManagerNote::class)]
    protected Collection $notes;

    /**
     * @var Collection<int, User>
     */
    #[MongoDB\EmbedMany(targetDocument: User::class)]
    protected Collection $users;

    #[MongoDB\Field(enumType: ReviewStatus::class)]
    protected ReviewStatus $reviewStatus = ReviewStatus::PENDING;

    #[Assert\Email]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $businessEmail = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $resellerId = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $subscriptionPlan = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $syncedOnce;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $enabledProviders = [];

    /**
     * @var Collection<int, Role>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Role::class)]
    protected Collection $enabledRoles;

    /**
     * @var Collection<int, Provider>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Provider::class)]
    protected Collection $virtualEnabledProviders;

    /**
     * @var Collection<int, Party>
     */
    #[MongoDB\EmbedMany(targetDocument: Party::class)]
    protected Collection $shareholders;

    /**
     * @var Collection<int, Party>
     */
    #[MongoDB\EmbedMany(targetDocument: Party::class)]
    protected Collection $directors;

    /**
     * @var Collection<int, Party>
     */
    #[MongoDB\EmbedMany(targetDocument: Party::class)]
    protected Collection $partners;

    #[MongoDB\EmbedOne(targetDocument: RiskInformation::class)]
    protected ?RiskInformation $riskInformation = null;

    #[MongoDB\EmbedOne(targetDocument: Branding::class)]
    protected Branding $branding;

    /**
     * @var Collection<int, Invitation>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Invitation::class)]
    protected Collection $invitations;

    /**
     * @var Collection<int, Registration>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Registration::class)]
    protected Collection $registrations;

    /**
     * @var Collection<int, ComplianceFile>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: ComplianceFile::class)]
    protected Collection $complianceFiles;

    #[MongoDB\EmbedOne(targetDocument: Metadata::class)]
    protected ?Metadata $metadata = null;

    #[MongoDB\EmbedOne(targetDocument: Address::class)]
    protected ?Address $tradingAddress = null;

    #[MongoDB\EmbedOne(targetDocument: Address::class)]
    protected ?Address $registeredAddress = null;

    #[MongoDB\Field(enumType: CreationSource::class)]
    protected ?CreationSource $creationSource = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $complianceFlag = false;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: RiskProfile::class)]
    protected ?RiskProfile $riskProfile = null;

    /**
     * @var Collection<int, PayoutBlock>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: PayoutBlock::class)]
    protected Collection $payoutBlocks;

    /**
     * @var Collection<int, Whitelist>
     */
    #[MongoDB\EmbedMany(targetDocument: Whitelist::class)]
    protected Collection $whitelists;

    /**
     * @var Collection<int, ProviderOnboarding>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: ProviderOnboarding::class)]
    protected Collection $providerOnboardings;

    /**
     * @var Collection<int, App>
     */
    #[MongoDB\ReferenceMany(targetDocument: App::class, mappedBy: 'company')]
    protected Collection $apps;

    /**
     * @var Collection<int, Location>
     */
    #[MongoDB\ReferenceMany(targetDocument: Location::class, mappedBy: 'company')]
    protected Collection $locations;

    #[MongoDB\EmbedOne(targetDocument: ResellerMetadata::class)]
    protected ?ResellerMetadata $resellerMetadata = null;

    /**
     * @var Collection<int, CompanyTerm>
     */
    #[MongoDB\ReferenceMany(targetDocument: CompanyTerm::class, mappedBy: 'company', sort: ['createdAt' => 'DESC'])]
    protected Collection $terms;

    #[MongoDB\ReferenceOne(targetDocument: FraudSignal::class, mappedBy: 'company')]
    protected ?FraudSignal $fraudSignal = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $onboardingRequireMoreInformation = false;

    #[Assert\PositiveOrZero]
    #[MongoDB\Field(type: MongoDBType::INT)]
    protected ?int $payoutOffset = null;

    #[Assert\Valid]
    #[MongoDB\ReferenceOne(targetDocument: CompanyBranding::class, cascade: 'persist', mappedBy: 'company')]
    protected ?CompanyBranding $companyBranding = null;

    public function __construct()
    {
        $this->directors = new ArrayCollection();
        $this->partners = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->enabledRoles = new ArrayCollection();
        $this->shareholders = new ArrayCollection();
        $this->apps = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->providerOnboardings = new ArrayCollection();
        $this->whitelists = new ArrayCollection();
        $this->payoutBlocks = new ArrayCollection();
        $this->complianceFiles = new ArrayCollection();
        $this->bankAccounts = new BankAccountCollection();
        $this->registrations = new ArrayCollection();
        $this->terms = new ArrayCollection();
    }

    public function getCompanyType(): ?Type
    {
        return $this->companyType;
    }

    public function setCompanyType(?Type $companyType): void
    {
        $this->companyType = $companyType;
    }

    public function getMessageType(): ?string
    {
        return $this->messageType;
    }

    public function setMessageType(?string $messageType): void
    {
        $this->messageType = $messageType;
    }

    public function getCompanyLegalType(): ?string
    {
        return $this->companyLegalType;
    }

    public function setCompanyLegalType(?string $companyLegalType): void
    {
        $this->companyLegalType = $companyLegalType;
    }

    public function getRegisteredName(): ?string
    {
        return $this->registeredName;
    }

    public function setRegisteredName(?string $registeredName): void
    {
        $this->registeredName = $registeredName;
    }

    public function getTradingName(): ?string
    {
        return $this->tradingName;
    }

    public function setTradingName(?string $tradingName): void
    {
        $this->tradingName = $tradingName;
    }

    public function getCompanyNumber(): ?string
    {
        return $this->companyNumber;
    }

    public function setCompanyNumber(?string $companyNumber): void
    {
        $this->companyNumber = $companyNumber;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(?string $vatNumber): void
    {
        $this->vatNumber = $vatNumber;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(?string $address1): void
    {
        $this->address1 = $address1;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): void
    {
        $this->address2 = $address2;
    }

    public function getGooglePlacesAddress(): ?string
    {
        return $this->googlePlacesAddress;
    }

    public function setGooglePlacesAddress(?string $googlePlacesAddress): void
    {
        $this->googlePlacesAddress = $googlePlacesAddress;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getBusinessWebsite(): ?string
    {
        return $this->businessWebsite;
    }

    public function setBusinessWebsite(?string $businessWebsite): void
    {
        $this->businessWebsite = $businessWebsite;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getRiskLevel(): ?RiskLevel
    {
        return $this->riskLevel;
    }

    public function setRiskLevel(?RiskLevel $riskLevel): void
    {
        $this->riskLevel = $riskLevel;
    }

    public function getEventPostUrl(): ?string
    {
        return $this->eventPostUrl;
    }

    public function setEventPostUrl(?string $eventPostUrl): void
    {
        $this->eventPostUrl = $eventPostUrl;
    }

    public function getLastReviewDate(): ?DateTimeInterface
    {
        return $this->lastReviewDate;
    }

    public function setLastReviewDate(?DateTimeInterface $lastReviewDate): void
    {
        $this->lastReviewDate = $lastReviewDate;
    }

    public function getReviewReference(): ?string
    {
        return $this->reviewReference;
    }

    public function setReviewReference(?string $reviewReference): void
    {
        $this->reviewReference = $reviewReference;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getCoupon(): ?string
    {
        return $this->coupon;
    }

    public function setCoupon(?string $coupon): void
    {
        $this->coupon = $coupon;
    }

    public function getMerchantCategoryCode(): ?string
    {
        return $this->merchantCategoryCode;
    }

    public function setMerchantCategoryCode(?string $merchantCategoryCode): void
    {
        $this->merchantCategoryCode = $merchantCategoryCode;
    }

    public function getBankAccounts(): BankAccountCollection
    {
        return $this->bankAccounts;
    }

    public function setBankAccounts(BankAccountCollection $bankAccounts): void
    {
        $this->bankAccounts = $bankAccounts;
    }

    public function getBankAccount(int $index): ?BankAccount
    {
        return $this->bankAccounts->get($index);
    }

    /**
     * @return Collection<int, AccountManagerNote>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    /**
     * @param Collection<int, AccountManagerNote> $notes
     */
    public function setNotes(Collection $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param Collection<int, User> $users
     */
    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }

    public function addUser(User $user): void
    {
        $this->users->add($user);
    }

    public function getUser(string $sub): ?User
    {
        $user = $this->getUsers()->filter(static fn (User $user) => $user->getSub() === $sub)->first();

        return false !== $user ? $user : null;
    }

    public function getReviewStatus(): ReviewStatus
    {
        return $this->reviewStatus;
    }

    public function setReviewStatus(ReviewStatus $reviewStatus): void
    {
        $this->reviewStatus = $reviewStatus;
    }

    public function getBusinessEmail(): ?string
    {
        return $this->businessEmail;
    }

    public function setBusinessEmail(?string $businessEmail): void
    {
        $this->businessEmail = $businessEmail;
    }

    public function getResellerId(): ?string
    {
        return $this->resellerId;
    }

    public function setResellerId(?string $resellerId): void
    {
        $this->resellerId = $resellerId;
    }

    public function getSubscriptionPlan(): ?string
    {
        return $this->subscriptionPlan;
    }

    public function setSubscriptionPlan(?string $subscriptionPlan): void
    {
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function isSyncedOnce(): bool
    {
        return $this->syncedOnce;
    }

    public function setSyncedOnce(bool $syncedOnce): void
    {
        $this->syncedOnce = $syncedOnce;
    }

    /**
     * @return string[]
     */
    public function getEnabledProviders(): array
    {
        return $this->enabledProviders;
    }

    /**
     * @param string[] $enabledProviders
     */
    public function setEnabledProviders(array $enabledProviders): void
    {
        $this->enabledProviders = $enabledProviders;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getEnabledRoles(): Collection
    {
        return $this->enabledRoles;
    }

    /**
     * @param Collection<int, Role> $enabledRoles
     */
    public function setEnabledRoles(Collection $enabledRoles): void
    {
        $this->enabledRoles = $enabledRoles;
    }

    /**
     * @return Collection<int, Party>
     */
    public function getShareholders(): Collection
    {
        return $this->shareholders;
    }

    /**
     * @param Collection<int, Party> $shareholders
     */
    public function setShareholders(Collection $shareholders): void
    {
        $this->shareholders = $shareholders;
    }

    /**
     * @return Collection<int, Party>
     */
    public function getDirectors(): Collection
    {
        return $this->directors;
    }

    /**
     * @param Collection<int, Party> $directors
     */
    public function setDirectors(Collection $directors): void
    {
        $this->directors = $directors;
    }

    /**
     * @return Collection<int, Party>
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    /**
     * @param Collection<int, Party> $partners
     */
    public function setPartners(Collection $partners): void
    {
        $this->partners = $partners;
    }

    public function getRiskInformation(): ?RiskInformation
    {
        return $this->riskInformation;
    }

    public function setRiskInformation(?RiskInformation $riskInformation): void
    {
        $this->riskInformation = $riskInformation;
    }

    public function getBranding(): Branding
    {
        return $this->branding;
    }

    public function setBranding(Branding $branding): void
    {
        $this->branding = $branding;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    /**
     * @param Collection<int, Invitation> $invitations
     */
    public function setInvitations(Collection $invitations): void
    {
        $this->invitations = $invitations;
    }

    /**
     * @return Collection<int, Registration>
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    /**
     * @param Collection<int, Registration> $registrations
     */
    public function setRegistrations(Collection $registrations): void
    {
        $this->registrations = $registrations;
    }

    /**
     * @return Collection<int, ComplianceFile>
     */
    public function getComplianceFiles(): Collection
    {
        return $this->complianceFiles;
    }

    /**
     * @param Collection<int, ComplianceFile> $complianceFiles
     */
    public function setComplianceFiles(Collection $complianceFiles): void
    {
        $this->complianceFiles = $complianceFiles;
    }

    public function getTradingAddress(): ?Address
    {
        return $this->tradingAddress;
    }

    public function setTradingAddress(?Address $tradingAddress): void
    {
        $this->tradingAddress = $tradingAddress;
    }

    public function getRegisteredAddress(): ?Address
    {
        return $this->registeredAddress;
    }

    public function setRegisteredAddress(?Address $registeredAddress): void
    {
        $this->registeredAddress = $registeredAddress;
    }

    public function getCreationSource(): ?CreationSource
    {
        return $this->creationSource;
    }

    public function setCreationSource(?CreationSource $creationSource): void
    {
        $this->creationSource = $creationSource;
    }

    public function getComplianceFlag(): bool
    {
        return $this->complianceFlag;
    }

    public function setComplianceFlag(bool $complianceFlag): void
    {
        $this->complianceFlag = $complianceFlag;
    }

    public function getRiskProfile(): ?RiskProfile
    {
        return $this->riskProfile;
    }

    public function setRiskProfile(?RiskProfile $riskProfile): void
    {
        $this->riskProfile = $riskProfile;
    }

    /**
     * @return Collection<int, PayoutBlock>
     */
    public function getPayoutBlocks(): Collection
    {
        return $this->payoutBlocks;
    }

    /**
     * @param Collection<int, PayoutBlock> $payoutBlocks
     */
    public function setPayoutBlocks(Collection $payoutBlocks): void
    {
        $this->payoutBlocks = $payoutBlocks;
    }

    /**
     * @return Collection<int, Whitelist>
     */
    public function getWhitelists(): Collection
    {
        return $this->whitelists;
    }

    /**
     * @param Collection<int, Whitelist> $whitelists
     */
    public function setWhitelists(Collection $whitelists): void
    {
        $this->whitelists = $whitelists;
    }

    /**
     * @return Collection<int, ProviderOnboarding>
     */
    public function getProviderOnboardings(): Collection
    {
        return $this->providerOnboardings;
    }

    /**
     * @param Collection<int, ProviderOnboarding> $providerOnboardings
     */
    public function setProviderOnboardings(Collection $providerOnboardings): void
    {
        $this->providerOnboardings = $providerOnboardings;
    }

    /**
     * Returns a company status based on reviewStatus and deleted flag {@see Status}.
     */
    public function getStatus(): Status
    {
        if ($this->isDeleted()) {
            return Status::TERMINATED;
        }

        if ($this->isBlacklisted()) {
            return Status::BLACKLISTED;
        }

        return $this->getReviewStatus()->toStatus();
    }

    /**
     * @return Collection<int, App>
     */
    public function getApps(): Collection
    {
        return $this->apps;
    }

    /**
     * @param Collection<int, App> $apps
     */
    public function setApps(Collection $apps): void
    {
        $this->apps = $apps;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    /**
     * @param Collection<int, Location> $locations
     */
    public function setLocations(Collection $locations): void
    {
        $this->locations = $locations;
    }

    public function getLocation(string $id): ?Location
    {
        $location = $this->getLocations()->filter(static fn (Location $location) => $id === $location->getId())->first();

        return false !== $location ? $location : null;
    }

    public function hasDefaultApp(): bool
    {
        return $this->apps->exists(fn (int $key, App $app) => $app->isDefault());
    }

    public function addNote(AccountManagerNote $note): void
    {
        $this->notes[] = $note;
    }

    public function getResellerMetadata(): ?ResellerMetadata
    {
        return $this->resellerMetadata;
    }

    public function setResellerMetadata(?ResellerMetadata $resellerMetadata): void
    {
        $this->resellerMetadata = $resellerMetadata;
    }

    /**
     * @return Collection<int, AccountManagerNote>
     */
    public function getActiveNotes(): Collection
    {
        return $this->getNotes()->filter(fn (AccountManagerNote $note) => !$note->isDeleted());
    }

    public function getNote(string $id): ?AccountManagerNote
    {
        $note = $this->getNotes()
            ->filter(static fn (AccountManagerNote $note) => $note->getId() === $id)
            ->first();

        return false !== $note ? $note : null;
    }

    public function __toString(): string
    {
        return $this->tradingName ?? $this->registeredName ?? $this->id ?? '';
    }

    /**
     * @return Collection<int, Provider>
     */
    public function getVirtualEnabledProviders(): Collection
    {
        return $this->virtualEnabledProviders;
    }

    /**
     * @param Collection<int, Provider> $virtualEnabledProviders
     */
    public function setVirtualEnabledProviders(Collection $virtualEnabledProviders): void
    {
        $this->virtualEnabledProviders = $virtualEnabledProviders;
    }

    public function hasPaymentGatewayMerchantID(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getPaymentGatewayMerchantID();
    }

    public function hasPaymentGatewayMerchantPWD(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getPaymentGatewayMerchantPWD();
    }

    public function hasSignatureType(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getSignatureType();
    }

    public function hasMpgsMerchantId(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getMpgsMerchantId();
    }

    public function hasMpgsGatewayPassword(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getMpgsGatewayPassword();
    }

    public function hasMpgsOnboarded(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return $this->getResellerMetadata()->isMpgsOnboarded();
    }

    public function hasUnionPayMerchantId(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getUnionPayMerchantId();
    }

    public function hasValitorMerchantCategoryCode(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getValitorMerchantCategoryCode();
    }

    public function hasContractId(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getContractId();
    }

    public function hasWeChatPaySubmerchantId(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getWeChatPaySubmerchantId();
    }

    public function hasWeChatPayOnboardingMcc(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        return '' !== $this->getResellerMetadata()->getWeChatPayOnboardingMcc();
    }

    public function isCompanyStructureComplete(): bool
    {
        if ($this->getShareholders()->isEmpty()) {
            return false;
        }

        if ($this->getDirectors()->isEmpty()) {
            return false;
        }

        foreach ($this->getShareholders() as $shareholder) {
            if (!$shareholder->isComplete()) {
                return false;
            }
        }

        return true;
    }

    public function isAddressComplete(): bool
    {
        if (null === $this->getRegisteredAddress() || null === $this->getTradingAddress()) {
            return false;
        }

        return true;
    }

    public function hasCommBankNationalId(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        // @todo method not exists
        // return "" !== $this->getResellerMetadata()->getCommBankNationalId();
        return false;
    }

    public function hasCommBankTransactionMaxAmount(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        // @todo method not exists
        // return "" !== $this->getResellerMetadata()->getCommBankTransactionMaxAmount();
        return false;
    }

    public function hasCommBankTransactionMinAmount(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        // @todo method not exists
        // return "" !== $this->getResellerMetadata()->getCommBankTransactionMinAmount();
        return false;
    }

    public function hasCommBankDailyAmountLimit(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        // @todo method not exists
        // return "" !== $this->getResellerMetadata()->getCommBankDailyAmountLimit();
        return false;
    }

    public function hasCommBankMonthlyAmountLimit(): bool
    {
        if (null === $this->getResellerMetadata()) {
            return false;
        }

        // @todo method not exists
        // return "" !== $this->getResellerMetadata()->getCommBankMonthlyAmountLimit();
        return false;
    }

    public function getValitorMerchantCategoryCode(): string
    {
        return $this->getResellerMetadata()?->getValitorMerchantCategoryCode() ?? '';
    }

    public function addEnabledProvider(string $provider): void
    {
        $this->enabledProviders[] = $provider;
    }

    public function isProviderEnabled(string $providerName): bool
    {
        return in_array($providerName, $this->enabledProviders, true);
    }

    public function getChoiceName(): ?string
    {
        return $this->__toString();
    }

    /**
     * @param Collection<int, CompanyTerm> $terms
     */
    public function setTerms(Collection $terms): void
    {
        $this->terms = $terms;
    }

    /**
     * @return Collection<int, CompanyTerm>
     */
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    public function getCurrentTerms(): ?CompanyTerm
    {
        return $this->terms[0] ?? null;
    }

    public function getMetadata(): ?Metadata
    {
        return $this->metadata;
    }

    public function setMetadata(?Metadata $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function hasFlaggedBrc(): bool
    {
        return false;
//        return $this->brc?->isInvalid() ?? false;
    }

    public function hasFlaggedUserIdentity(): bool
    {
        foreach ($this->users as $user) {
            if ($user->hasFlaggedIdentity()) {
                return true;
            }
        }

        return false;
    }

    public function getFraudSignal(): ?FraudSignal
    {
        return $this->fraudSignal;
    }

    public function setFraudSignal(?FraudSignal $fraudSignal): void
    {
        $this->fraudSignal = $fraudSignal;
    }

    public function isOnboardingRequireMoreInformation(): bool
    {
        return $this->onboardingRequireMoreInformation;
    }

    public function setOnboardingRequireMoreInformation(bool $onboardingRequireMoreInformation): void
    {
        $this->onboardingRequireMoreInformation = $onboardingRequireMoreInformation;
    }

    /**
     * @return array<string, string|null>
     */
    public function getAddressArray(): array
    {
        return [
            'address1' => $this->address1,
            'address2' => $this->address2,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postalCode' => $this->postalCode,
        ];
    }

    public function isEmptyAddress(): bool
    {
        return null === $this->address1
            && null === $this->address2
            && null === $this->city
            && null === $this->state
            && null === $this->country
            && null === $this->postalCode;
    }

    public function isVerified(): bool
    {
        return ReviewStatus::VERIFIED === $this->reviewStatus;
    }

    public function getPayoutOffset(): ?int
    {
        return $this->payoutOffset;
    }

    public function setPayoutOffset(?int $payoutOffset): void
    {
        $this->payoutOffset = $payoutOffset;
    }

    public function getCompanyBranding(): ?CompanyBranding
    {
        return $this->companyBranding;
    }

    public function setCompanyBranding(?CompanyBranding $companyBranding): void
    {
        $this->companyBranding = $companyBranding;
        $companyBranding?->setCompany($this);
    }
}
