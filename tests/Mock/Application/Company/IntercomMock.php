<?php

declare(strict_types=1);

namespace App\Tests\Mock\Application\Company;

use App\Application\Company\IntercomInterface;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;

class IntercomMock implements IntercomInterface
{
    public function syncUser(Company $company, User $user): void
    {
    }
}
