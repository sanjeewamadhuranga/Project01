<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener\Security;

use App\Application\Listener\Security\InvalidSignInAttemptListener;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionClass;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class InvalidSignInAttemptListenerTest extends UnitTestCase
{
    private int $maxInvalidSignInAttempts;

    protected function setUp(): void
    {
        parent::setUp();

        $reflectionClass = new ReflectionClass(InvalidSignInAttemptListener::class);
        $this->maxInvalidSignInAttempts = $reflectionClass->getConstant('MAX_INVALID_SIGN_IN_ATTEMPTS');
    }

    public function testItClearsInvalidSignInAttemptsOnAuthenticationSuccess(): void
    {
        $user = $this->createMock(Administrator::class);
        $user->expects(self::once())->method('clearInvalidSignInAttempts');

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $event = new AuthenticationSuccessEvent($token);

        $subscriber = new InvalidSignInAttemptListener($this->createStub(EventDispatcherInterface::class), $this->getManagerRegistry());
        $subscriber->onAuthenticationSuccess($event);
    }

    public function testItDoesNotSuspendAccountIfMaxInvalidSignInAttemptsIsNotReached(): void
    {
        $user = $this->createMock(Administrator::class);
        $user->expects(self::once())->method('increaseInvalidSignInAttempts');
        $user->expects(self::never())->method('suspendAccount');
        $user->method('getInvalidSignInAttempts')->willReturn($this->maxInvalidSignInAttempts - 1);

        $event = $this->getLoginFailureEvent($user);
        $subscriber = new InvalidSignInAttemptListener($this->createStub(EventDispatcherInterface::class), $this->getManagerRegistry());
        $subscriber->onAuthenticationFailure($event);
    }

    public function testItSuspendAccountAndDispatchEventIfMaxInvalidSignInAttemptsIsReached(): void
    {
        $user = $this->createMock(Administrator::class);
        $user->expects(self::once())->method('increaseInvalidSignInAttempts');
        $user->expects(self::once())->method('suspendAccount');
        $user->method('getInvalidSignInAttempts')->willReturn($this->maxInvalidSignInAttempts);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects(self::once())->method('dispatch');

        $event = $this->getLoginFailureEvent($user);
        $subscriber = new InvalidSignInAttemptListener($eventDispatcher, $this->getManagerRegistry());
        $subscriber->onAuthenticationFailure($event);
    }

    private function getManagerRegistry(): ManagerRegistry
    {
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects(self::once())->method('flush');

        $managerRegistry = $this->createStub(ManagerRegistry::class);
        $managerRegistry->method('getManager')->willReturn($objectManager);

        return $managerRegistry;
    }

    private function getLoginFailureEvent(Administrator $user): LoginFailureEvent
    {
        $userBadge = $this->createStub(UserBadge::class);
        $userBadge->method('getUser')->willReturn($user);

        $passport = $this->createStub(Passport::class);
        $passport->method('getBadge')->willReturn($userBadge);

        $event = $this->createStub(LoginFailureEvent::class);
        $event->method('getPassport')->willReturn($passport);

        return $event;
    }
}
