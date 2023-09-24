<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener\Security;

use App\Application\Listener\Security\AuthenticationListener;
use App\Domain\ActivityLog\ActivityLogType;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\LogRepository;
use App\Tests\Unit\UnitTestCase;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class AuthenticationListenerTest extends UnitTestCase
{
    public function testItCreatesAndSavesLogOnAuthenticationSuccessAndUpdatesLastLoginDate(): void
    {
        $user = $this->createMock(Administrator::class);
        $user->expects(self::once())->method('setLastLogin')->with(self::isInstanceOf(DateTimeInterface::class));
        $token = $this->getToken($user);
        $event = new AuthenticationSuccessEvent($token);

        $subscriber = $this->getSubscriber(ActivityLogType::AUTHENTICATION_SUCCESS);
        $subscriber->onAuthenticationSuccess($event);
    }

    public function testItCreatesAndSavesLogOnAuthenticationFailure(): void
    {
        $user = $this->createMock(Administrator::class);
        $user->expects(self::never())->method('setLastLogin');

        $userBadge = $this->createStub(UserBadge::class);
        $userBadge->method('getUser')->willReturn($user);

        $passport = $this->createStub(Passport::class);
        $passport->method('getBadge')->willReturn($userBadge);

        $event = $this->createStub(LoginFailureEvent::class);
        $event->method('getPassport')->willReturn($passport);

        $subscriber = $this->getSubscriber(ActivityLogType::AUTHENTICATION_FAILURE);
        $subscriber->onAuthenticationFailure($event);
    }

    private function getToken(Administrator $user): TokenInterface
    {
        $token = $this->createStub(NullToken::class);
        $token->method('getUser')->willReturn($user);
        $token->method('getUserIdentifier')->willReturn('aaaa-bbbb-cccc-dddd');

        return $token;
    }

    private function getSubscriber(string $createLogParam): AuthenticationListener
    {
        $logRepository = $this->createMock(LogRepository::class);
        $logRepository->expects(self::once())
            ->method('save')
            ->with(
                self::logicalAnd(
                    self::isInstanceOf(Log::class),
                    self::callback(static fn (Log $log): bool => $createLogParam === $log->getType())
                )
            );

        $request = $this->createStub(RequestStack::class);
        $request->method('getCurrentRequest')->willReturn(new Request());

        return new AuthenticationListener($request, $logRepository);
    }
}
