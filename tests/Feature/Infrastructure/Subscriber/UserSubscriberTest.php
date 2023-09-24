<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\Subscriber;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Tests\Feature\BaseTestCase;

class UserSubscriberTest extends BaseTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = self::getContainer()->get(UserRepository::class);
    }

    public function testUserDataWillBeCanonicalizeOnPrePersist(): void
    {
        $email = uniqid().'@EXAMPLE.COM';
        $user = new Administrator();
        $user->setEmail($email);

        $this->userRepository->save($user);

        self::assertSame($user->getEmailCanonical(), mb_strtolower($email));
        self::assertSame($user->getUserIdentifier(), mb_strtolower($email));
        self::assertSame($user->getUsernameCanonical(), mb_strtolower($email));
    }

    public function testUserPasswordWillChangeOnPreUpdateWhenPlainPasswordProvided(): void
    {
        $user = new Administrator();
        $user->setPlainPassword(uniqid());

        $this->userRepository->save($user);
        $passwordHash = $user->getPassword();

        $user->setPlainPassword(uniqid());
        $user->setEmail(uniqid().'@testing.com');
        $this->userRepository->save($user);

        self::assertNotSame($passwordHash, $user->getPassword());
    }
}
