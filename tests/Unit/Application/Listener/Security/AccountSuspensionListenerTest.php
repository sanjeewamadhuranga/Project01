<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener\Security;

use App\Application\Listener\Security\AccountSuspensionListener;
use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Domain\Event\User\AccountSuspendedEvent;
use App\Infrastructure\Repository\Security\ManagerPortalRoleRepository;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Tests\Unit\UnitTestCase;
use ArrayIterator;
use Symfony\Component\Mailer\MailerInterface;

class AccountSuspensionListenerTest extends UnitTestCase
{
    public function testItSendsMailOnAccountSuspension(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send');

        $user = $this->createStub(Administrator::class);
        $user->method('getEmail')->willReturn('test@pay.com');

        $managerPortalRoleRepository = $this->createStub(ManagerPortalRoleRepository::class);
        $managerPortalRoleRepository->method('findOneBy')->willReturn($this->createStub(ManagerPortalRole::class));

        $userRepository = $this->createStub(UserRepository::class);
        $userRepository->method('getUsersByRole')->willReturn(new ArrayIterator([$user]));

        $accountSuspendedEvent = new AccountSuspendedEvent($user);

        $subscriber = new AccountSuspensionListener($mailer, $userRepository, $managerPortalRoleRepository);
        $subscriber->__invoke($accountSuspendedEvent);
    }

    public function testItDoesNotSendEmailWhenThereIsNoRoleWithNameAdmin(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');

        $managerPortalRoleRepository = $this->createStub(ManagerPortalRoleRepository::class);
        $managerPortalRoleRepository->method('findOneBy')->willReturn(null);

        $subscriber = new AccountSuspensionListener($mailer, $this->createStub(UserRepository::class), $managerPortalRoleRepository);
        $subscriber->__invoke(new AccountSuspendedEvent($this->createStub(Administrator::class)));
    }

    public function testItDoesNotSendEmailWhenNoUserWithAdminRole(): void
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');

        $managerPortalRoleRepository = $this->createStub(ManagerPortalRoleRepository::class);
        $managerPortalRoleRepository->method('findOneBy')->willReturn($this->createStub(ManagerPortalRole::class));

        $userRepository = $this->createStub(UserRepository::class);
        $userRepository->method('getUsersByRole')->willReturn(new ArrayIterator([]));

        $subscriber = new AccountSuspensionListener($mailer, $userRepository, $managerPortalRoleRepository);
        $subscriber->__invoke(new AccountSuspendedEvent($this->createStub(Administrator::class)));
    }
}
