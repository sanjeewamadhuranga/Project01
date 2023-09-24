<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Listener;

use App\Application\Locale\LocaleSetter;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Listener\LocaleListener;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LocaleListenerTest extends UnitTestCase
{
    private readonly LocaleSetter&MockObject $localeSetter;

    private readonly LocaleListener $subscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->localeSetter = $this->createMock(LocaleSetter::class);
        $this->subscriber = new LocaleListener($this->localeSetter);
    }

    public function testItSetsUserLocaleOnInteractiveLogin(): void
    {
        $userLocale = 'ru';
        $user = $this->createStub(Administrator::class);
        $user->method('getLocale')->willReturn($userLocale);

        $authenticationToken = $this->createStub(TokenInterface::class);
        $authenticationToken->method('getUser')->willReturn($user);

        $session = $this->createMock(SessionInterface::class);
        $session->expects(self::once())->method('set')->with('_locale', $userLocale);

        $request = $this->createStub(Request::class);
        $request->method('getSession')->willReturn($session);

        $this->subscriber->onInteractiveLogin(new InteractiveLoginEvent($request, $authenticationToken));
    }

    public function testItPassesRequestToLocaleSetter(): void
    {
        $request = $this->createStub(Request::class);
        $this->localeSetter->expects(self::once())->method('setLocale')->with($request);
        $this->subscriber->onKernelRequest(new RequestEvent($this->createStub(HttpKernel::class), $request, null));
    }
}
