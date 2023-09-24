<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Subscriber;

use App\Application\Security\UserUpdater;
use App\Domain\Document\Company\User as CompanyUser;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Subscriber\UserSubscriber;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserSubscriberTest extends UnitTestCase
{
    public function testPrePersistWillRunUpdateUser(): void
    {
        $user = $this->createMock(Administrator::class);

        $userUpdater = $this->createMock(UserUpdater::class);
        $userUpdater->expects(self::once())->method('updateUser')->with($user);

        $lifecycleEventArgs = $this->createMock(LifecycleEventArgs::class);
        $lifecycleEventArgs->expects(self::once())->method('getObject')->willReturn($user);

        $userSubscriber = new UserSubscriber($userUpdater);
        $userSubscriber->prePersist($lifecycleEventArgs);
    }

    public function testPrePersistWontRunUpdateUserWhenWrongUserProvided(): void
    {
        $user = $this->createMock(CompanyUser::class);

        $userUpdater = $this->createMock(UserUpdater::class);
        $userUpdater->expects(self::never())->method('updateUser')->with($user);

        $lifecycleEventArgs = $this->createMock(LifecycleEventArgs::class);
        $lifecycleEventArgs->expects(self::once())->method('getObject')->willReturn($user);

        $userSubscriber = new UserSubscriber($userUpdater);
        $userSubscriber->prePersist($lifecycleEventArgs);
    }

    public function testPreUpdateWillRunUpdateUser(): void
    {
        $user = $this->createMock(Administrator::class);

        $userUpdater = $this->createMock(UserUpdater::class);
        $userUpdater->expects(self::once())->method('updateUser')->with($user);

        $lifecycleEventArgs = $this->createMock(LifecycleEventArgs::class);
        $lifecycleEventArgs->expects(self::once())->method('getObject')->willReturn($user);
        $lifecycleEventArgs->expects(self::once())->method('getObjectManager');

        $userSubscriber = new UserSubscriber($userUpdater);
        $userSubscriber->preUpdate($lifecycleEventArgs);
    }

    public function testPreUpdateWontRunUpdateUserWhenWrongUserProvided(): void
    {
        $user = $this->createMock(CompanyUser::class);

        $userUpdater = $this->createMock(UserUpdater::class);
        $userUpdater->expects(self::never())->method('updateUser')->with($user);

        $lifecycleEventArgs = $this->createMock(LifecycleEventArgs::class);
        $lifecycleEventArgs->expects(self::once())->method('getObject')->willReturn($user);
        $lifecycleEventArgs->expects(self::never())->method('getObjectManager');

        $userSubscriber = new UserSubscriber($userUpdater);
        $userSubscriber->preUpdate($lifecycleEventArgs);
    }
}
