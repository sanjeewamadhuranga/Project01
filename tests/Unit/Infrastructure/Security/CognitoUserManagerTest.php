<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Security;

use App\Application\Security\CognitoUser;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Domain\Settings\FederatedIdentityType;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Security\CognitoUserManager;
use App\Tests\Unit\UnitTestCase;
use AsyncAws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use AsyncAws\CognitoIdentityProvider\Enum\UserStatusType;
use AsyncAws\CognitoIdentityProvider\Result\AdminCreateUserResponse;
use AsyncAws\CognitoIdentityProvider\Result\AdminResetUserPasswordResponse;
use AsyncAws\CognitoIdentityProvider\Result\AdminSetUserPasswordResponse;
use AsyncAws\CognitoIdentityProvider\Result\AdminUpdateUserAttributesResponse;
use AsyncAws\CognitoIdentityProvider\Result\ListUsersResponse;
use AsyncAws\CognitoIdentityProvider\ValueObject\AttributeType;
use AsyncAws\CognitoIdentityProvider\ValueObject\UserType;
use AsyncAws\Core\Result;
use AsyncAws\Core\Test\ResultMockFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;

/**
 * @group security
 */
class CognitoUserManagerTest extends UnitTestCase
{
    private CognitoIdentityProviderClient&MockObject $identityProviderClient;

    private SystemSettings&Stub $systemSettings;

    private CognitoUserManager $cognitoUserManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->identityProviderClient = $this->createMock(CognitoIdentityProviderClient::class);
        $this->systemSettings = $this->createStub(SystemSettings::class);
        $this->cognitoUserManager = new CognitoUserManager($this->identityProviderClient, 'test-pool', $this->systemSettings);
    }

    public function testItTransformsUsers(): void
    {
        $this->expectListsUsers($this->getAwsUsers());
        foreach ($this->cognitoUserManager->listUsers() as $user) {
            self::assertSame(UserStatusType::CONFIRMED, $user->getStatus());
        }
    }

    public function testItFindsUserBySub(): void
    {
        $awsUser = $this->getUserType('testUser', '5b69893a-ca0b-4a56-953c-6f2bc484781d', 'test@example.com', '+44123123123');
        $this->expectListsUsers([$awsUser], 'sub = "5b69893a-ca0b-4a56-953c-6f2bc484781d"', 1);
        $user = $this->cognitoUserManager->getUserBySub('5b69893a-ca0b-4a56-953c-6f2bc484781d');

        self::assertInstanceOf(CognitoUser::class, $user);
        self::assertSame($awsUser->getUsername(), $user->getUsername());
    }

    public function testItGetTheFirstUserBySub(): void
    {
        $this->expectListsUsers([$this->getAwsUsers()[0]], 'sub = "5b69893a-ca0b-4a56-953c-6f2bc484781a"');
        $resultUser = $this->cognitoUserManager->getUserByIdentifier(CognitoUser::ATTRIBUTE_SUB, '5b69893a-ca0b-4a56-953c-6f2bc484781a');
        self::assertInstanceOf(CognitoUser::class, $resultUser);

        self::assertSame('5b69893a-ca0b-4a56-953c-6f2bc484781a', $resultUser->getSub());
    }

    public function testItFindsUserByVerifiedEmail(): void
    {
        $this->expectListsUsers($this->getAwsUsers(), 'email = "test@example.com"');
        $resultUser = $this->cognitoUserManager->getUserByIdentifier(CognitoUser::ATTRIBUTE_EMAIL, 'test@example.com');
        self::assertInstanceOf(CognitoUser::class, $resultUser);
        self::assertTrue($resultUser->isEmailVerified());
        self::assertSame('onlyEmailVerified', $resultUser->getUsername());
    }

    public function testItResponsePhoneNumberWhenProvidePhoneNumberAsIdentifier(): void
    {
        $this->expectListsUsers($this->getAwsUsers(), 'phone_number = "+44123123123"');
        $resultUser = $this->cognitoUserManager->getUserByIdentifier(CognitoUser::ATTRIBUTE_PHONE_NUMBER, '+44123123123');
        self::assertInstanceOf(CognitoUser::class, $resultUser);
        self::assertTrue($resultUser->isPhoneVerified());
        self::assertSame('onlyPhoneNumberVerified', $resultUser->getUsername());
    }

    public function testItReturnNullWhenProvideNotRelevantPhoneNumberAsIdentifier(): void
    {
        $this->expectListsUsers([], 'phone_number = "+6587175894"');
        self::assertNull($this->cognitoUserManager->getUserByIdentifier(CognitoUser::ATTRIBUTE_PHONE_NUMBER, '+6587175894'));
    }

    public function testItReturnNullWhenProvideRelevantPhoneNumberButDidNotVerified(): void
    {
        $this->expectListsUsers([$this->getAwsUsers()[0]], 'phone_number = "+44123123123"');
        self::assertNull($this->cognitoUserManager->getUserByIdentifier(CognitoUser::ATTRIBUTE_PHONE_NUMBER, '+44123123123'));
    }

    public function testItReturnNullWhenProvideRelevantEmailButDidNotVerified(): void
    {
        $this->expectListsUsers([$this->getAwsUsers()[0]], 'email = "test@example.com"');
        self::assertNull($this->cognitoUserManager->getUserByIdentifier(CognitoUser::ATTRIBUTE_EMAIL, 'test@example.com'));
    }

    public function testItReturnsNullWhenUserIsNotFound(): void
    {
        $this->expectListsUsers([], 'sub = "5b69893a-ca0b-4a56-953c-6f2bc4847811"', 1);
        self::assertNull($this->cognitoUserManager->getUserBySub('5b69893a-ca0b-4a56-953c-6f2bc4847811'));
    }

    public function testItUpdatesUserAttribute(): void
    {
        $this->identityProviderClient->expects(self::once())
            ->method('adminUpdateUserAttributes')
            ->with([
                'UserPoolId' => 'test-pool',
                'Username' => 'user1',
                'UserAttributes' => [new AttributeType(['Name' => 'email', 'Value' => 'example@example.com'])],
            ])->willReturn(ResultMockFactory::create(AdminUpdateUserAttributesResponse::class));

        $this->cognitoUserManager->updateUserAttribute('user1', 'email', 'example@example.com');
    }

    /**
     * @dataProvider usernameDataProvider
     */
    public function testItCreatesIdentityFromUserWithBothEmailAndMobileVerified(FederatedIdentityType $identityType, string $expectedUsername): void
    {
        $this->systemSettings->method('getFederatedIdentityType')->willReturn($identityType);

        $sub = '5b69893a-ca0b-4a56-953c-6f2bc484781a';
        $user = new User();
        $user->setMobile(' +44-123-12-31-23');
        $user->setContactEmail('example@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setTemporaryPassword('test-pass');
        $user->setNationalIdentity('N1234');

        $company = new Company();
        $company->setId('123456789');

        $response = ResultMockFactory::create(AdminCreateUserResponse::class, [
            'User' => $this->getUserType($expectedUsername, $sub, (string) $user->getContactEmail(), (string) $user->getMobile(), true, true),
        ]);

        $this->identityProviderClient->expects(self::once())
            ->method('adminCreateUser')
            ->with([
                'DesiredDeliveryMediums' => ['EMAIL'],
                'MessageAction' => 'SUPPRESS',
                'Username' => $expectedUsername,
                'TemporaryPassword' => $user->getTemporaryPassword(),
                'UserPoolId' => 'test-pool',
                'UserAttributes' => [
                        new AttributeType(['Name' => 'name', 'Value' => 'John Doe']),
                        new AttributeType(['Name' => 'phone_number', 'Value' => '+44123123123']),
                        new AttributeType(['Name' => 'email', 'Value' => 'example@example.com']),
                        new AttributeType(['Name' => 'email_verified', 'Value' => 'true']),
                        new AttributeType(['Name' => 'phone_number_verified', 'Value' => 'true']),
                        new AttributeType(['Name' => 'custom:defaultCompany', 'Value' => '123456789']),
                        new AttributeType(['Name' => 'custom:nationalIdentity', 'Value' => 'N1234']),
                    ],
                'ValidationData' => [
                    ['Name' => 'adminAdded', 'Value' => 'true'],
                ],
            ])
            ->willReturn($response);

        $this->identityProviderClient->expects(self::never())->method('adminSetUserPassword');

        $this->cognitoUserManager->createIdentityFromUser($user, $company);
        self::assertSame('test-pass', $user->getTemporaryPassword());
        self::assertSame($sub, $user->getId());
        self::assertSame($sub, $user->getSub());
    }

    public function testItSetsRandomPasswordIfPasswordLessLoginIsEnabled(): void
    {
        $this->systemSettings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);
        $this->systemSettings->method('hasFederatedPasswordlessLogin')->willReturn(true);

        $sub = '5b69893a-ca0b-4a56-953c-6f2bc484781a';
        $user = new User();
        $user->setMobile(' +44-123-12-31-23');
        $user->setContactEmail('example@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setTemporaryPassword('test-pass');

        $response = ResultMockFactory::create(AdminCreateUserResponse::class, [
            'User' => new UserType(['Attributes' => [new AttributeType(['Name' => 'sub', 'Value' => $sub])]]),
        ]);

        $this->identityProviderClient->expects(self::once())
            ->method('adminCreateUser')
            ->with(self::isType('array'))
            ->willReturn($response);

        $this->identityProviderClient->expects(self::once())
            ->method('adminSetUserPassword')
            ->with(self::callback(static function (array $input) use ($user): bool {
                self::assertNotSame($user->getTemporaryPassword(), $input['Password']);
                self::assertSame(16, strlen($input['Password']));
                self::assertTrue($input['Permanent']);
                self::assertSame(sha1('44123123123'), $input['Username']);
                self::assertSame('test-pool', $input['UserPoolId']);

                return true;
            }))
            ->willReturn(ResultMockFactory::create(AdminSetUserPasswordResponse::class, []));

        $this->cognitoUserManager->createIdentityFromUser($user, null);
        self::assertNull($user->getTemporaryPassword());
        self::assertSame($sub, $user->getId());
        self::assertSame($sub, $user->getSub());
    }

    /**
     * @dataProvider usernameDataProvider
     */
    public function testItAdminResetUserPassword(FederatedIdentityType $federatedIdentityType, string $expectedUsername): void
    {
        $this->systemSettings->method('getFederatedIdentityType')->willReturn($federatedIdentityType);
        $user = $this->createStub(CognitoUser::class);
        $user->method('getPhoneNumber')->willReturn('+44123123123');
        $user->method('getEmail')->willReturn('example@example.com');
        $user->method('getUsername')->willReturn($expectedUsername);

        $this->identityProviderClient->expects(self::once())
            ->method('adminResetUserPassword')
            ->with([
                'UserPoolId' => 'test-pool',
                'Username' => $expectedUsername,
            ])->willReturn(ResultMockFactory::create(AdminResetUserPasswordResponse::class))
        ;

        $this->cognitoUserManager->adminResetUserPassword($user);
    }

    public function testDeleteUser(): void
    {
        $user = $this->createStub(CognitoUser::class);
        $user->method('getUserName')->willReturn('test-username');

        $this->identityProviderClient->expects(self::once())
            ->method('adminDeleteUser')
            ->with([
                'Username' => 'test-username',
                'UserPoolId' => 'test-pool',
            ])->willReturn(ResultMockFactory::create(Result::class));

        $this->cognitoUserManager->deleteUser($user);
    }

    /**
     * @return iterable<array{FederatedIdentityType, string}>
     */
    public function usernameDataProvider(): iterable
    {
        yield 'email uses email SHA1' => [FederatedIdentityType::EMAIL, sha1('example@example.com')];
        yield 'phone number uses phone number SHA1' => [FederatedIdentityType::PHONE_NUMBER, sha1('44123123123')];
    }

    /**
     * @param UserType[] $users
     */
    private function expectListsUsers(array $users, ?string $filter = null, ?int $limit = null): void
    {
        $response = ResultMockFactory::create(ListUsersResponse::class, ['Users' => $users]);

        $this->identityProviderClient->expects(self::once())
            ->method('listUsers')
            ->with([
                'UserPoolId' => 'test-pool',
                'Filter' => $filter,
                'Limit' => $limit,
                'PaginationToken' => null,
            ])
            ->willReturn($response);
    }

    /**
     * @return UserType[]
     */
    private function getAwsUsers(): array
    {
        return [
            $this->getUserType('bothEmailAndPhoneNumbersNotVerified', '5b69893a-ca0b-4a56-953c-6f2bc484781a', 'test@example.com', '+44123123123'),
            $this->getUserType('onlyPhoneNumberVerified', '5b69893a-ca0b-4a56-953c-6f2bc484781d', 'test@example.com', '+44123123123', false, true),
            $this->getUserType('onlyEmailVerified', '5b69893a-ca0b-4a56-953c-6f2bc484781f', 'test@example.com', '+44123123123', true, false),
            $this->getUserType('bothEmailAndPhoneNumberVerified', '5b69893a-ca0b-4a56-953c-6f2bc484781a', 'test@example.com', '+44123123123', true, true),
        ];
    }

    private function getUserType(string $username, string $subId, string $email, string $phoneNumber, bool $isVerifiedEmail = false, bool $isVerifiedPhone = false): UserType
    {
        return new UserType([
            'Username' => $username,
            'UserStatus' => UserStatusType::CONFIRMED,
            'Attributes' => [
                new AttributeType(['Name' => 'name', 'Value' => 'John Doe']),
                new AttributeType(['Name' => 'sub', 'Value' => $subId]),
                new AttributeType(['Name' => 'phone_number', 'Value' => $phoneNumber]),
                new AttributeType(['Name' => 'email', 'Value' => $email]),
                new AttributeType(['Name' => 'email_verified', 'Value' => $isVerifiedEmail ? 'true' : 'false']),
                new AttributeType(['Name' => 'phone_number_verified', 'Value' => $isVerifiedPhone ? 'true' : 'false']),
            ],
        ]);
    }
}
