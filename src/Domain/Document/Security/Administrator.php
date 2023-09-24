<?php

declare(strict_types=1);

namespace App\Domain\Document\Security;

use App\Application\Security\MaskField;
use App\Application\Validation\Company\PhoneNumber;
use App\Domain\Document\BaseDocument;
use App\Domain\Document\Interfaces\Activeable;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Infrastructure\Security\Gravatar;
use App\Infrastructure\Validator\ProtectedRole;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Scheb\TwoFactorBundle\Model\PreferredProviderInterface;
use Stringable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Unique(fields: ['usernameCanonical'], errorPath: 'email')]
#[MongoDB\HasLifecycleCallbacks]
#[MongoDB\Document(collection: 'administrators', repositoryClass: UserRepository::class)]
class Administrator extends BaseDocument implements UserInterface, Stringable, PasswordAuthenticatedUserInterface, TwoFactorInterface, Activeable, NormalizableInterface, PreferredProviderInterface
{
    private const NUMBER_OF_PREVIOUS_PASSWORDS_REMEMBERED = 24;
    private const SUSPENSION_PERIOD = 'PT30M';
    final public const PASSWORD_VALIDITY_PERIOD = 'P90D';

    final public const MFA_GOOGLE = 'google';
    final public const MFA_SMS = 'sms';

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $locale = null;

    #[Serializer\Ignore]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $googleId = null;

    /**
     * Encrypted password. Must be persisted.
     */
    #[Serializer\Ignore]
    #[MaskField]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $password = null;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     */
    #[Serializer\Ignore]
    protected ?string $plainPassword = null;

    /**
     * User permission list (cached from all roles). Must not be persisted.
     *
     * @var string[]|null
     */
    protected ?array $permissions = null;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $lastLogin = null;

    #[Assert\Email]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $email = '';

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $username;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    #[MongoDB\Index(order: 'asc', unique: true)]
    protected string $emailCanonical;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    #[MongoDB\Index(order: 'asc', unique: true)]
    protected string $usernameCanonical;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $enabled = true;

    #[Serializer\Ignore]
    #[MaskField]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $confirmationToken = null;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $passwordRequestedAt = null;

    /**
     * @var Collection<int, ManagerPortalRole>
     */
    #[Assert\Count(min: 1), ProtectedRole]
    #[MongoDB\ReferenceMany(storeAs: 'id', targetDocument: ManagerPortalRole::class)]
    protected Collection $managerPortalRoles;

    #[Serializer\Ignore]
    #[MaskField]
    #[MongoDB\Field(name: 'googleAuthenticatorSecret', type: 'string')]
    protected ?string $googleAuthenticatorSecret = null;

    #[Serializer\Ignore]
    #[MongoDB\Field(name: 'googlePicture', type: 'string')]
    protected ?string $googlePicture = null;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $passwordExpiry = null;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $accountSuspendedDate = null;

    #[MongoDB\Field(type: MongoDBType::DATE)]
    protected ?DateTimeInterface $accountExpirationDate = null;

    /**
     * @var array<int, string>
     */
    #[Serializer\Ignore]
    #[MongoDB\Field(type: 'collection')]
    #[MaskField]
    protected array $previousPasswords = [];

    #[MongoDB\Field(type: MongoDBType::INT)]
    protected int $invalidSignInAttempts = 0;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $fluidLayout = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $condensedLayout = false;

    #[PhoneNumber]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $phoneNumber = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $isSmsAuthenticationEnabled = false;

    public function __construct()
    {
        $this->managerPortalRoles = new ArrayCollection();
        $this->passwordExpiry = new DateTime();
    }

    public function updatePasswordExpiration(): void
    {
        $this->setPasswordExpiry((new DateTime())->add(new DateInterval(self::PASSWORD_VALIDITY_PERIOD)));
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = null !== $locale ? strtolower($locale) : null;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): void
    {
        $this->googleId = $googleId;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getLastLogin(): ?DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTimeInterface $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
        $this->emailCanonical = mb_strtolower($email);
        $this->username = mb_strtolower($email);
        $this->usernameCanonical = mb_strtolower($email);
    }

    public function getEmailCanonical(): string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(string $emailCanonical): void
    {
        $this->emailCanonical = $emailCanonical;
    }

    public function getUsernameCanonical(): string
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical(string $usernameCanonical): void
    {
        $this->usernameCanonical = $usernameCanonical;
    }

    /**
     * @return Collection<int, ManagerPortalRole>
     */
    public function getManagerPortalRoles(): Collection
    {
        return $this->managerPortalRoles;
    }

    /**
     * @param Collection<int, ManagerPortalRole> $managerPortalRoles
     */
    public function setManagerPortalRoles(Collection $managerPortalRoles): void
    {
        $this->managerPortalRoles = $managerPortalRoles;
    }

    public function addManagerPortalRole(ManagerPortalRole $managerPortalRole): void
    {
        if ($this->managerPortalRoles->contains($managerPortalRole)) {
            return;
        }

        $this->managerPortalRoles->add($managerPortalRole);
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getPasswordRequestedAt(): ?DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?DateTimeInterface $passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    #[Serializer\Ignore]
    public function isPasswordRequestExpired(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof DateTimeInterface &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl <= time();
    }

    public function __toString(): string
    {
        return $this->emailCanonical;
    }

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return (bool) $this->googleAuthenticatorSecret;
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->username;
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->googleAuthenticatorSecret;
    }

    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void
    {
        $this->googleAuthenticatorSecret = $googleAuthenticatorSecret;
    }

    public function getPasswordExpiry(): ?DateTimeInterface
    {
        return $this->passwordExpiry;
    }

    public function setPasswordExpiry(?DateTimeInterface $passwordExpiry): void
    {
        $this->passwordExpiry = $passwordExpiry;
    }

    public function isPasswordExpired(): bool
    {
        return null !== $this->passwordExpiry && $this->passwordExpiry < new DateTime();
    }

    public function hasFluidLayout(): bool
    {
        return $this->fluidLayout;
    }

    public function setFluidLayout(bool $fluidLayout): void
    {
        $this->fluidLayout = $fluidLayout;
    }

    public function hasCondensedLayout(): bool
    {
        return $this->condensedLayout;
    }

    public function setCondensedLayout(bool $condensedLayout): void
    {
        $this->condensedLayout = $condensedLayout;
    }

    /**
     * @return string[]
     */
    public function getPermissions(bool $refresh = false): array
    {
        if (null === $this->permissions || $refresh) {
            $permissions = [];

            foreach ($this->getManagerPortalRoles() as $role) {
                array_push($permissions, ...$role->getPermissions(), ...$role->getNewPermissions());
            }

            $this->permissions = array_values(array_unique($permissions));
            sort($this->permissions);
        }

        return $this->permissions;
    }

    /**
     * @return string[]
     */
    public function getPreviousPasswords(): array
    {
        return $this->previousPasswords;
    }

    public function addPreviousPassword(string $previousPasswords): void
    {
        $this->previousPasswords[] = $previousPasswords;
        $this->previousPasswords = array_values(array_slice($this->previousPasswords, -self::NUMBER_OF_PREVIOUS_PASSWORDS_REMEMBERED, self::NUMBER_OF_PREVIOUS_PASSWORDS_REMEMBERED, true));
    }

    public function getInvalidSignInAttempts(): int
    {
        return $this->invalidSignInAttempts;
    }

    public function increaseInvalidSignInAttempts(): void
    {
        ++$this->invalidSignInAttempts;
    }

    public function clearInvalidSignInAttempts(): void
    {
        $this->invalidSignInAttempts = 0;
    }

    public function isSuspended(): bool
    {
        return null !== $this->accountSuspendedDate && $this->accountSuspendedDate > new DateTime();
    }

    public function suspendAccount(): void
    {
        $this->accountSuspendedDate = (new DateTime())->add(new DateInterval(self::SUSPENSION_PERIOD));
        $this->invalidSignInAttempts = 0;
    }

    public function resetSuspension(): void
    {
        $this->accountSuspendedDate = null;
    }

    public function getAccountSuspendedDate(): ?DateTimeInterface
    {
        return $this->accountSuspendedDate;
    }

    public function isActive(): bool
    {
        return $this->enabled;
    }

    public function setActive(bool $active): void
    {
        $this->enabled = $active;
    }

    public function getAccountExpirationDate(): ?DateTimeInterface
    {
        return $this->accountExpirationDate;
    }

    public function setAccountExpirationDate(?DateTimeInterface $accountExpirationDate): void
    {
        $this->accountExpirationDate = $accountExpirationDate;
    }

    public function isExpired(): bool
    {
        return null !== $this->accountExpirationDate && new DateTime() > $this->accountExpirationDate;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function isSmsAuthenticationEnabled(): bool
    {
        return null !== $this->phoneNumber && $this->isSmsAuthenticationEnabled;
    }

    public function setIsSmsAuthenticationEnabled(bool $isSmsAuthenticationEnabled): void
    {
        $this->isSmsAuthenticationEnabled = $isSmsAuthenticationEnabled;
    }

    public function is2FaEnabled(): bool
    {
        return $this->isSmsAuthenticationEnabled() || $this->isGoogleAuthenticatorEnabled();
    }

    #[MongoDB\PostLoad]
    public function postLoad(): void
    {
        if (is_null($this->passwordExpiry)) {
            $this->passwordExpiry = (new DateTime())->sub(new DateInterval('PT1S'));
            if (!is_null($this->createdAt)) {
                $this->passwordExpiry = (new DateTime($this->createdAt->format('Y-m-d H:i:s')))->add(new DateInterval('P90D'));
            }
        }
    }

    /**
     * @return array{id: string|null, username: string, password: string|null, enabled: bool}
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'enabled' => $this->enabled,
        ];
    }

    /**
     * @param array{id: string|null, username: string, password: string, enabled: bool} $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->enabled = $data['enabled'];
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array|string|int|float|bool
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'enabled' => $this->enabled,
        ];
    }

    public function getGooglePicture(): ?string
    {
        return $this->googlePicture;
    }

    public function setGooglePicture(?string $picture): void
    {
        $this->googlePicture = $picture;
    }

    public function getAvatar(): string
    {
        return $this->getGooglePicture() ?? Gravatar::getAvatar($this->getEmail());
    }

    /**
     * Use Google Authenticator as primary authentication method.
     */
    public function getPreferredTwoFactorProvider(): ?string
    {
        return self::MFA_GOOGLE;
    }

    public function reset2fa(): void
    {
        $this->setGoogleAuthenticatorSecret(null);
        $this->setIsSmsAuthenticationEnabled(false);
    }

    /**
     * Scrambles user email after deletion.
     */
    public function scrambleEmail(): void
    {
        $time = time();
        $this->setUsername(sprintf('deleted_%d_%s', $time, $this->getUsername()));
        $this->setUsernameCanonical(sprintf('deleted_%d_%s', $time, $this->getUsernameCanonical()));
        $this->setEmail(sprintf('deleted_%d_%s', $time, $this->getEmail()));
        $this->setEmailCanonical(sprintf('deleted_%d_%s', $time, $this->getEmailCanonical()));
    }
}
