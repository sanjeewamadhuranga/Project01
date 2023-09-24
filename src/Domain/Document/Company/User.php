<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Application\Security\CognitoUser;
use App\Application\Validation\Company\PhoneNumber;
use App\Domain\Company\UserStatus;
use App\Domain\Document\BaseDocument;
use App\Domain\Document\Role\Role;
use App\Domain\Document\Traits\Blacklistable;
use App\Domain\Settings\FederatedIdentityType;
use App\Infrastructure\Security\Gravatar;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\EmbeddedDocument]
#[MongoDB\HasLifecycleCallbacks]
class User extends BaseDocument
{
    use Blacklistable;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $id = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $sub = null;

    /**
     * @var Collection<int, Role>
     */
    #[MongoDB\Field(type: 'collection')]
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Role::class)]
    protected Collection $roles;

    #[MongoDB\Field(enumType: UserStatus::class)]
    protected UserStatus $state = UserStatus::ACTIVE;

    #[Assert\NotBlank, Assert\Email]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $contactEmail = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $email = null;

    #[Assert\NotBlank(groups: ['kyc'])]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $dob = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $contactName = null;

    #[Assert\NotBlank]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $firstName = null;

    #[Assert\NotBlank]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $lastName = null;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $emailPreferences = [];

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $pushNotificationPreferences = [];

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $betaFeatures = [];

    #[PhoneNumber]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $mobile = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected ?bool $twoStepAuthEnabled = null;

    #[MongoDB\Field(type: MongoDBType::RAW)]
    protected mixed $image = null;

    /**
     * Not stored in DB anymore.
     */
    protected ?string $temporaryPassword = null;

    #[Assert\Valid, Assert\NotBlank(groups: ['kyc'])]
    #[MongoDB\EmbedOne(targetDocument: Address::class)]
    protected ?Address $addresses = null;

    #[Assert\Valid]
    #[MongoDB\EmbedOne(targetDocument: ComplianceReport::class)]
    protected ?ComplianceReport $complianceReport = null;

//    #[Assert\Valid]
//    #[MongoDB\EmbedOne(targetDocument: NationalIdentity::class)]
    #[Assert\Regex(pattern: '/^([0-9]{9}[x|X|v|V]|^[1-2]{1}[0-9]{11})$/')]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $nationalIdentity = null;

    protected bool $requireKyc = false;

    #[MongoDB\EmbedOne(targetDocument: UserMetadata::class)]
    protected ?UserMetadata $metadata = null;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public static function fromCognitoUser(CognitoUser $cognitoUser): self
    {
        $user = new self();
        [$firstName, $lastName] = array_pad(explode(' ', (string) $cognitoUser->getName()), 2, null);
        $user->setSub($cognitoUser->getSub());
        $user->setContactEmail($cognitoUser->getEmail());
        $user->setMobile($cognitoUser->getPhoneNumber());
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        if (null !== $cognitoUser->getNationalIdentity()) {
            $user->setNationalIdentity($cognitoUser->getNationalIdentity());
        }

        return $user;
    }

    public static function fromIdentifier(FederatedIdentityType $identityType, string $identifier): self
    {
        $user = new self();

        if (FederatedIdentityType::EMAIL === $identityType) {
            $user->setContactEmail($identifier);
        }

        if (FederatedIdentityType::PHONE_NUMBER === $identityType) {
            $user->setMobile($identifier);
        }

        return $user;
    }

    public function getSub(): ?string
    {
        return $this->sub;
    }

    public function setSub(?string $sub): void
    {
        $this->id = $sub;
        $this->sub = $sub;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param Collection<int, Role> $roles
     */
    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
    }

    public function getState(): UserStatus
    {
        return $this->state;
    }

    public function setState(UserStatus $state): void
    {
        $this->state = $state;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): void
    {
        $this->email = $contactEmail;
        $this->contactEmail = $contactEmail;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->setContactEmail($email);
    }

    public function getDobDate(): ?DateTime
    {
        return null === $this->dob ? null : new DateTime($this->dob);
    }

    public function getDob(): ?string
    {
        return $this->dob;
    }

    public function setDob(?string $dob): void
    {
        $this->dob = $dob;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    #[MongoDB\PreUpdate]
    #[MongoDB\PrePersist]
    public function setContactName(): void
    {
        $this->contactName = $this->firstName.' '.$this->lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
        $this->setContactName();
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
        $this->setContactName();
    }

    /**
     * @return string[]
     */
    public function getEmailPreferences(): array
    {
        return $this->emailPreferences;
    }

    /**
     * @param string[] $emailPreferences
     */
    public function setEmailPreferences(array $emailPreferences): void
    {
        $this->emailPreferences = $emailPreferences;
    }

    /**
     * @return string[]
     */
    public function getPushNotificationPreferences(): array
    {
        return $this->pushNotificationPreferences;
    }

    /**
     * @param string[] $pushNotificationPreferences
     */
    public function setPushNotificationPreferences(array $pushNotificationPreferences): void
    {
        $this->pushNotificationPreferences = $pushNotificationPreferences;
    }

    /**
     * @return string[]
     */
    public function getBetaFeatures(): array
    {
        return $this->betaFeatures;
    }

    /**
     * @param string[] $betaFeatures
     */
    public function setBetaFeatures(array $betaFeatures): void
    {
        $this->betaFeatures = $betaFeatures;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function isTwoStepAuthEnabled(): ?bool
    {
        return $this->twoStepAuthEnabled;
    }

    public function setTwoStepAuthEnabled(?bool $twoStepAuthEnabled): void
    {
        $this->twoStepAuthEnabled = $twoStepAuthEnabled;
    }

    public function getImage(): mixed
    {
        return $this->image;
    }

    public function setImage(mixed $image): void
    {
        $this->image = $image;
    }

    public function getTemporaryPassword(): ?string
    {
        return $this->temporaryPassword;
    }

    public function setTemporaryPassword(?string $temporaryPassword): void
    {
        $this->temporaryPassword = $temporaryPassword;
    }

    public function getAddresses(): ?Address
    {
        return $this->addresses;
    }

    public function setAddresses(?Address $addresses): void
    {
        $this->addresses = $addresses;
    }

    public function getComplianceReport(): ?ComplianceReport
    {
        return $this->complianceReport;
    }

    public function setComplianceReport(?ComplianceReport $complianceReport): void
    {
        $this->complianceReport = $complianceReport;
    }

    public function getId(): ?string
    {
        return $this->getSub();
    }

    public function getAvatar(): string
    {
        return Gravatar::getAvatar($this->getEmail() ?? '');
    }

    public function getNationalIdentity(): ?string
    {
        return $this->nationalIdentity;
    }

    public function setNationalIdentity(?string $nationalIdentity): void
    {
        $this->nationalIdentity = $nationalIdentity;
    }

    public function hasFlaggedIdentity(): bool
    {
        return false;
//        return $this->nationalIdentity?->isInvalid() ?? false;
    }

    public function isRequireKyc(): bool
    {
        return $this->requireKyc;
    }

    public function setRequireKyc(bool $requireKyc): void
    {
        $this->requireKyc = $requireKyc;
    }

    public function getMetadata(): ?UserMetadata
    {
        return $this->metadata;
    }

    public function setMetadata(?UserMetadata $metadata): void
    {
        $this->metadata = $metadata;
    }
}
