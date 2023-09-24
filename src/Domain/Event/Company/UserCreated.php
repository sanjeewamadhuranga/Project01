<?php

declare(strict_types=1);

namespace App\Domain\Event\Company;

use App\Application\Security\CognitoUser;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;

final class UserCreated
{
    public function __construct(private readonly Company $company, private readonly User $user, private ?CognitoUser $cognitoUser = null)
    {
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setCognitoUser(CognitoUser $cognitoUser): void
    {
        $this->cognitoUser = $cognitoUser;
    }

    public function getCognitoUser(): ?CognitoUser
    {
        return $this->cognitoUser;
    }
}
