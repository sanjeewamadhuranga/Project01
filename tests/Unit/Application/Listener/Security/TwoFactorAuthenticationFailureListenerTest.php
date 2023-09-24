<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener\Security;

use App\Application\Listener\Security\TwoFactorAuthenticationFailureListener;
use App\Domain\ActivityLog\ActivityLogType;
use App\Domain\Document\Log\Details;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\LogRepository;
use App\Tests\Unit\UnitTestCase;

use function PHPUnit\Framework\once;

use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvent;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TwoFactorAuthenticationFailureListenerTest extends UnitTestCase
{
    public function testItSavesLogWithUserAndRequestData(): void
    {
        $username = 'someUsername';
        $clientIp = '89.226.28.215';
        $userAgent = 'Dalvik/2.1.0 (Linux; U; Android 10; MI 9 MIUI/V11.0.8.0.QFAEUXM)';

        $logRepository = $this->createMock(LogRepository::class);
        $logRepository->expects(once())->method('save')->with(self::callback(static fn ($log) => $log instanceof Log &&
            ActivityLogType::AUTHENTICATION_2FA_FAILURE === $log->getType() &&
            $log->getDetails() instanceof Details &&
            $username === $log->getDetails()->getUsername() &&
            $clientIp === $log->getDetails()->getIp() &&
            $userAgent === $log->getDetails()->getUserAgent()));

        $event = $this->getTwoFactorAuthenticationEvent($username, $clientIp, $userAgent);
        (new TwoFactorAuthenticationFailureListener($logRepository))($event);
    }

    private function getTwoFactorAuthenticationEvent(string $username, string $clientIp, string $userAgent): TwoFactorAuthenticationEvent
    {
        $user = $this->createStub(Administrator::class);
        $user->method('getUsername')->willReturn($username);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $request = $this->createStub(Request::class);
        $request->method('getClientIp')->willReturn($clientIp);
        $request->headers = new HeaderBag(['user-agent' => $userAgent]);

        return new TwoFactorAuthenticationEvent($request, $token);
    }
}
