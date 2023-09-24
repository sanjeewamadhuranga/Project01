<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Security\CognitoUser;
use App\Application\Security\CognitoUserCollection;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Domain\Settings\FederatedIdentityType;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Security\Exception\CognitoUserException;
use AsyncAws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use AsyncAws\CognitoIdentityProvider\ValueObject\AttributeType;
use AsyncAws\CognitoIdentityProvider\ValueObject\UserType;
use Traversable;
use UnexpectedValueException;

/**
 * Wraps AWS Cognito client and encapsulates some of our domain logic on top of Cognito Identity Provider.
 *
 * @see CognitoUser
 */
class CognitoUserManager implements CognitoUserManagerInterface
{
    public function __construct(
        private readonly CognitoIdentityProviderClient $cognitoClient,
        private readonly string $userPoolId,
        private readonly SystemSettings $settings
    ) {
    }

    public function listUsers(?string $filter = null, ?int $limit = null, ?string $page = null, bool $onlyCurrentPage = false): CognitoUserCollection
    {
        $result = $this->cognitoClient->listUsers([
            'UserPoolId' => $this->userPoolId,
            'Filter' => $filter,
            'PaginationToken' => $page,
            'Limit' => $limit,
        ]);

        return new CognitoUserCollection(
            $this->transformUsers($result->getUsers($onlyCurrentPage)),
            $result->getPaginationToken()
        );
    }

    /**
     * @return iterable<CognitoUser>
     */
    public function listUsersByIdentifier(string $identifierType, string $identifier, ?int $limit = null): iterable
    {
        return $this->listUsers(sprintf('%s = "%s"', $identifierType, self::sanitize($identifier)), $limit);
    }

    public function getUserByIdentifier(string $identifierType, string $identifier): ?CognitoUser
    {
        foreach ($this->listUsersByIdentifier($identifierType, $identifier) as $user) {
            if (CognitoUser::ATTRIBUTE_SUB === $identifierType) {
                return $user;
            }

            if (CognitoUser::ATTRIBUTE_PHONE_NUMBER === $identifierType && $user->isPhoneVerified()) {
                return $user;
            }

            if (CognitoUser::ATTRIBUTE_EMAIL === $identifierType && $user->isEmailVerified()) {
                return $user;
            }
        }

        return null;
    }

    public function getUserBySub(string $sub): ?CognitoUser
    {
        foreach ($this->listUsersByIdentifier(CognitoUser::ATTRIBUTE_SUB, $sub, 1) as $user) {
            return $user;
        }

        return null;
    }

    public function createIdentityFromUser(User $user, ?Company $company): CognitoUser
    {
        $user->setMobile(str_replace([' ', '-'], '', (string) $user->getMobile()));
        $username = $this->getCognitoUsername($user);

        $result = $this->cognitoClient->adminCreateUser([
            'DesiredDeliveryMediums' => ['EMAIL'],
            'MessageAction' => 'SUPPRESS',
            'Username' => $username,
            'TemporaryPassword' => $user->getTemporaryPassword(),
            'UserPoolId' => $this->userPoolId,
            'UserAttributes' => $this->getAttributes($user, $company),
            'ValidationData' => [
                ['Name' => 'adminAdded', 'Value' => 'true'],
            ],
        ]);

        $cognitoUser = CognitoUser::fromCognitoUserType($result->getUser() ?? throw new CognitoUserException('Could not create cognito user', $result->info()));

        if ($this->settings->hasFederatedPasswordlessLogin()) {
            $this->cognitoClient->adminSetUserPassword([
                'Password' => PasswordGenerator::generate(16), // Set a new, unknown password to disallow logging in using it
                'Permanent' => true,
                'Username' => $username,
                'UserPoolId' => $this->userPoolId,
            ])->resolve();
            $user->setTemporaryPassword(null);
        }

        $sub = $cognitoUser->getSub();

        $user->setId($sub);
        $user->setSub($sub);

        return $cognitoUser;
    }

    public function adminResetUserPassword(CognitoUser $user): void
    {
        $this->cognitoClient->adminResetUserPassword([
            'UserPoolId' => $this->userPoolId,
            'Username' => $user->getUsername(),
        ])->resolve();
    }

    public function updateUserAttribute(string $username, string $attribute, ?string $value): void
    {
        $this->cognitoClient->adminUpdateUserAttributes([
            'UserPoolId' => $this->userPoolId,
            'Username' => $username,
            'UserAttributes' => [new AttributeType(['Name' => $attribute, 'Value' => $value])],
        ])->resolve();
    }

    public static function sanitize(string $parameter): string
    {
        return addslashes($parameter);
    }

    public function deleteUser(CognitoUser $user): void
    {
        $this->cognitoClient->adminDeleteUser([
            'Username' => $user->getUsername(),
            'UserPoolId' => $this->userPoolId,
        ])->resolve();
    }

    private function getCognitoUsername(User $user): string
    {
        $identityType = $this->settings->getFederatedIdentityType();

        if (FederatedIdentityType::EMAIL === $identityType) {
            return sha1(strtolower((string) $user->getContactEmail()));
        }

        if (FederatedIdentityType::PHONE_NUMBER === $identityType) {
            return sha1(strtolower(str_replace('+', '', (string) $user->getMobile())));
        }

        throw new UnexpectedValueException('Invalid identity type provided');
    }

    /**
     * @return AttributeType[]
     */
    private function getAttributes(User $user, ?Company $company): array
    {
        return array_filter([
            AttributeType::create(['Name' => CognitoUser::ATTRIBUTE_NAME, 'Value' => $user->getContactName()]),
            AttributeType::create(['Name' => CognitoUser::ATTRIBUTE_PHONE_NUMBER, 'Value' => $user->getMobile()]),
            AttributeType::create(['Name' => CognitoUser::ATTRIBUTE_EMAIL, 'Value' => strtolower((string) $user->getContactEmail())]),
            AttributeType::create(['Name' => CognitoUser::ATTRIBUTE_EMAIL_VERIFIED, 'Value' => 'true']),
            AttributeType::create(['Name' => CognitoUser::ATTRIBUTE_PHONE_NUMBER_VERIFIED, 'Value' => 'true']),
            AttributeType::create(['Name' => CognitoUser::ATTRIBUTE_DEFAULT_COMPANY_ID, 'Value' => $company?->getId()]),
            AttributeType::create(['Name' => CognitoUser::ATTRIBUTE_NATIONAL_IDENTITY, 'Value' => $user->getNationalIdentity()]),
        ], static fn (AttributeType $attribute): bool => null !== $attribute->getValue());
    }

    /**
     * @param iterable<UserType> $users
     *
     * @return Traversable<CognitoUser>
     */
    private function transformUsers(iterable $users): Traversable
    {
        foreach ($users as $user) {
            yield CognitoUser::fromCognitoUserType($user);
        }
    }
}
