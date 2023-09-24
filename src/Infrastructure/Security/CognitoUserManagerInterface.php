<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Security\CognitoUser;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;

interface CognitoUserManagerInterface
{
    public function getUserBySub(string $sub): ?CognitoUser;

    public function createIdentityFromUser(User $user, ?Company $company): CognitoUser;

    public function getUserByIdentifier(string $identifierType, string $identifier): ?CognitoUser;
}
