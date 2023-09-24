<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security;

use App\Application\Security\UserUpdater;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class UserUpdaterTest extends UnitTestCase
{
    public function testItTransformsUserProperties(): void
    {
        $user = new Administrator();
        $user->setEmail('test@EXAMPLE.com');
        $passwordHasher = $this->createMock(UserPasswordHasher::class);
        $updater = new UserUpdater($passwordHasher);
        $passwordHasher->expects(self::never())->method('hashPassword');
        $updater->updateUser($user);
        $canonicalEmail = 'test@example.com';

        self::assertSame($canonicalEmail, $user->getUserIdentifier());
        self::assertSame($canonicalEmail, $user->getUsernameCanonical());
        self::assertSame($canonicalEmail, $user->getEmailCanonical());
        self::assertSame('test@EXAMPLE.com', $user->getEmail());
    }

    public function testItEncodesPassword(): void
    {
        $user = new Administrator();
        $user->setPlainPassword('test');
        $user->setEmail('test@EXAMPLE.com');
        $passwordHasher = $this->createMock(UserPasswordHasher::class);
        $updater = new UserUpdater($passwordHasher);
        $passwordHasher->expects(self::once())->method('hashPassword')->with($user, 'test')->willReturn('h4sh3d p4ss');
        $updater->updateUser($user);

        self::assertSame('test@example.com', $user->getUserIdentifier());
        self::assertSame('h4sh3d p4ss', $user->getPassword());
        self::assertNull($user->getPlainPassword());
    }
}
