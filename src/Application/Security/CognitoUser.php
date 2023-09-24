<?php

declare(strict_types=1);

namespace App\Application\Security;

use AsyncAws\CognitoIdentityProvider\ValueObject\UserType;
use DateTimeImmutable;

/**
 * Represents a Cognito user from AWS response. Allows easy access to our custom attributes without looping trough them.
 */
class CognitoUser
{
    final public const ATTRIBUTE_SUB = 'sub';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_PHONE_NUMBER = 'phone_number';
    final public const ATTRIBUTE_PHONE_NUMBER_VERIFIED = 'phone_number_verified';
    final public const ATTRIBUTE_EMAIL = 'email';
    final public const ATTRIBUTE_EMAIL_VERIFIED = 'email_verified';
    final public const ATTRIBUTE_DEFAULT_COMPANY_ID = 'custom:defaultCompany';
    final public const ATTRIBUTE_STATUS = 'cognito:user_status';
    final public const ATTRIBUTE_NATIONAL_IDENTITY = 'custom:nationalIdentity';

    private string $sub;

    private ?string $status = null;

    private ?string $username = null;

    private ?bool $enabled = null;

    private ?string $name = null;

    private ?string $email = null;

    private ?string $phoneNumber = null;

    private bool $emailVerified = false;

    private bool $phoneVerified = false;

    private ?DateTimeImmutable $createdAt = null;

    private ?DateTimeImmutable $updatedAt = null;

    private ?string $defaultCompanyId = null;

    private ?string $nationalIdentity = null;

    /** @var array<string, string|null> */
    private array $attributes = [];

    public static function fromCognitoUserType(UserType $cognitoUser): self
    {
        $user = new self();
        $user->status = $cognitoUser->getUserStatus();
        $user->username = $cognitoUser->getUsername();
        $user->enabled = $cognitoUser->getEnabled();
        $user->createdAt = $cognitoUser->getUserCreateDate();
        $user->updatedAt = $cognitoUser->getUserLastModifiedDate();

        foreach ($cognitoUser->getAttributes() as $attribute) {
            $value = $attribute->getValue();
            switch ($attribute->getName()) {
                case self::ATTRIBUTE_NAME:
                    $user->name = $value;
                    break;
                case self::ATTRIBUTE_SUB:
                    $user->sub = (string) $value;
                    break;
                case self::ATTRIBUTE_PHONE_NUMBER:
                    $user->phoneNumber = $value;
                    break;
                case self::ATTRIBUTE_EMAIL:
                    $user->email = $value;
                    break;
                case self::ATTRIBUTE_EMAIL_VERIFIED:
                    $user->emailVerified = ('true' === $value);
                    break;
                case self::ATTRIBUTE_PHONE_NUMBER_VERIFIED:
                    $user->phoneVerified = ('true' === $value);
                    break;
                case self::ATTRIBUTE_DEFAULT_COMPANY_ID:
                    $user->defaultCompanyId = $value;
                    break;
                case self::ATTRIBUTE_NATIONAL_IDENTITY:
                    $user->nationalIdentity = $value;
                    break;
            }

            $user->attributes[$attribute->getName()] = $value;
        }

        return $user;
    }

    public function getSub(): string
    {
        return $this->sub;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function isPhoneVerified(): bool
    {
        return $this->phoneVerified;
    }

    public function getDefaultCompanyId(): ?string
    {
        return $this->defaultCompanyId;
    }

    public function getNationalIdentity(): ?string
    {
        return $this->nationalIdentity;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return array<string, string|null>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
