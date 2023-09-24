<?php

declare(strict_types=1);

namespace App\Tests\Mock\Infrastructure\Security;

use App\Application\Security\CognitoUser;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Infrastructure\Security\CognitoUserManagerInterface;
use AsyncAws\CognitoIdentityProvider\ValueObject\AttributeType;
use AsyncAws\CognitoIdentityProvider\ValueObject\UserType;

class CognitoUserManagerMock implements CognitoUserManagerInterface
{
    public function getUserByIdentifier(string $identifierType, string $identifier): ?CognitoUser
    {
        return null;
    }

    public function getUserBySub(string $sub): ?CognitoUser
    {
        return null;
    }

    public function createIdentityFromUser(User $user, ?Company $company): CognitoUser
    {
        return $this->getTestCognitoUser();
    }

    private function getTestCognitoUser(): CognitoUser
    {
        $userType = new UserType([
            'Attributes' => [
                new AttributeType(['Name' => CognitoUser::ATTRIBUTE_SUB, 'Value' => 'test-sub-123']),
            ],
        ]);

        return CognitoUser::fromCognitoUserType($userType);
    }
}
