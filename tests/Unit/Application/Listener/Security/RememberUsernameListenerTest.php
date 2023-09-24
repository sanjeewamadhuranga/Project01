<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener\Security;

use App\Application\Listener\Security\RememberUsernameListener;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class RememberUsernameListenerTest extends UnitTestCase
{
    private RememberUsernameListener $subscriber;

    private LoginSuccessEvent&Stub $event;

    private Response&Stub $response;

    private Request&Stub $request;

    private ResponseHeaderBag&MockObject $responseHeaders;

    protected function setUp(): void
    {
        $this->subscriber = new RememberUsernameListener();

        $this->request = $this->createStub(Request::class);
        $this->response = $this->createStub(Response::class);
        $this->responseHeaders = $this->createMock(ResponseHeaderBag::class);
        $this->response->headers = $this->responseHeaders;

        $this->event = $this->createStub(LoginSuccessEvent::class);
        $this->event->method('getRequest')->willReturn($this->request);
        $this->event->method('getResponse')->willReturn($this->response);
    }

    public function testItSetsCookieWhenRememberMeIsChecked(): void
    {
        $username = 'test@pay.com';

        $this->request->request = new InputBag([
            'remember' => 'on',
            'username' => $username,
        ]);

        $this->responseHeaders->expects(self::once())->method('setCookie')->with(
            self::callback(static fn (Cookie $cookie) => 'last_username' === $cookie->getName() && $username === $cookie->getValue())
        );

        $this->subscriber->__invoke($this->event);
    }

    public function testItRemovesCookieWhenRememberMeIsNotChecked(): void
    {
        $this->request->request = new InputBag(['username' => 'some@pay.com']);

        $this->responseHeaders->expects(self::once())->method('setCookie')->with(
            self::callback(static fn (Cookie $cookie) => 'last_username' === $cookie->getName() && '' === $cookie->getValue())
        );

        $this->subscriber->__invoke($this->event);
    }
}
