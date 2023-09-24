<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security;

use App\Application\Security\CognitoUser;
use App\Tests\Unit\UnitTestCase;
use AsyncAws\CognitoIdentityProvider\Enum\UserStatusType;
use AsyncAws\CognitoIdentityProvider\ValueObject\AttributeType;
use AsyncAws\CognitoIdentityProvider\ValueObject\UserType;

class CognitoUserTest extends UnitTestCase
{
    /**
     * @dataProvider cognitoUserProvider
     */
    public function testItCanBeCreatedFromCognitoUserObject(UserType $awsUser, string $sub, bool $emailConfirmed, bool $phoneConfirmed): void
    {
        $user = CognitoUser::fromCognitoUserType($awsUser);
        self::assertSame($awsUser->getUserStatus(), $user->getStatus());
        self::assertSame($sub, $user->getSub());
        self::assertSame($emailConfirmed, $user->isEmailVerified());
        self::assertSame($phoneConfirmed, $user->isPhoneVerified());
        self::assertCount(count($awsUser->getAttributes()), $user->getAttributes());
    }

    /**
     * @return iterable<array{UserType, string, bool, bool}>
     */
    public function cognitoUserProvider(): iterable
    {
        yield 'active user' => [
            new UserType([
                'UserStatus' => UserStatusType::CONFIRMED,
                'Attributes' => [
                    new AttributeType(['Name' => 'name', 'Value' => 'John Doe']),
                    new AttributeType(['Name' => 'sub', 'Value' => '5b69893a-ca0b-4a56-953c-6f2bc484781f']),
                    new AttributeType(['Name' => 'phone_number', 'Value' => '+44123123123']),
                    new AttributeType(['Name' => 'email', 'Value' => 'test@example.com']),
                    new AttributeType(['Name' => 'email_verified', 'Value' => 'true']),
                    new AttributeType(['Name' => 'phone_number_verified', 'Value' => 'false']),
                ],
            ]),
            '5b69893a-ca0b-4a56-953c-6f2bc484781f',
            true,
            false,
        ];

        yield 'archived user' => [
            new UserType([
                'UserStatus' => UserStatusType::ARCHIVED,
                'Attributes' => [
                    new AttributeType(['Name' => 'name', 'Value' => 'John Doe']),
                    new AttributeType(['Name' => 'sub', 'Value' => '5b69893a-ca0b-4a56-953c-6f2bc484781d']),
                    new AttributeType(['Name' => 'phone_number', 'Value' => '+44123123123']),
                    new AttributeType(['Name' => 'email', 'Value' => 'test@example.com']),
                    new AttributeType(['Name' => 'email_verified', 'Value' => 'false']),
                    new AttributeType(['Name' => 'phone_number_verified', 'Value' => 'true']),
                ],
            ]),
            '5b69893a-ca0b-4a56-953c-6f2bc484781d',
            false,
            true,
        ];
    }
}
