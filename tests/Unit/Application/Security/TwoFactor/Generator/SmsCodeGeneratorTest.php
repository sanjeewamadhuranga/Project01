<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security\TwoFactor\Generator;

use App\Application\Security\TwoFactor\Generator\SmsCodeGenerator;
use App\Application\Security\TwoFactor\SmsCodeDto;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SmsCodeGeneratorTest extends UnitTestCase
{
    private TexterInterface&MockObject $texter;

    private RequestStack&MockObject $requestStack;

    private SmsCodeGenerator $codeGenerator;

    private TranslatorInterface&MockObject $translator;

    protected function setUp(): void
    {
        $this->texter = $this->createMock(TexterInterface::class);

        $this->requestStack = $this->createMock(RequestStack::class);

        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->codeGenerator = new SmsCodeGenerator($this->texter, $this->requestStack, $this->translator);
    }

    public function testItThrowsErrorWhenUserDoNotHavePhoneNumber(): void
    {
        $userName = 'someUserName';
        $user = $this->createMock(Administrator::class);
        $user->method('getPhoneNumber')->willReturn(null);
        $user->method('getUsername')->willReturn($userName);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('User %s do not have phone number.', $userName));

        $this->codeGenerator->generateAndSend($user);
    }

    public function testItGeneratesSmsCodeSavesItAndSendsToUser(): void
    {
        $userPhoneNumber = 'somePhoneNumber';

        $session = $this->createMock(SessionInterface::class);
        $session->expects(self::once())->method('set')->with('sms_code', self::callback(fn (SmsCodeDto $smsCode) => !$smsCode->isExpired() && $this->validateSmsCode($smsCode->code)));
        $this->requestStack->method('getSession')->willReturn($session);

        $smsContent = 'someSms';
        $this->translator->expects(self::once())->method('trans')
            ->with('security.2fa.smsTemplate', self::callback(fn ($transParams) => $this->validateSmsCode($transParams['%code%'])))
            ->willReturn($smsContent);
        $this->texter->expects(self::once())->method('send')
            ->with(self::callback(static fn (SmsMessage $message) => $userPhoneNumber === $message->getPhone() && $smsContent === $message->getSubject()));

        $user = $this->createMock(Administrator::class);
        $user->method('getPhoneNumber')->willReturn($userPhoneNumber);

        $this->codeGenerator->generateAndSend($user);
    }

    public function testItCompareProvidedCodeWithOneSavedInSession(): void
    {
        $smsCode = new SmsCodeDto();
        $expectedCode = $smsCode->code;

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('sms_code')->willReturn($smsCode);

        $this->requestStack->method('getSession')->willReturn($session);

        self::assertTrue($this->codeGenerator->validateCode($expectedCode));
    }

    public function testCodeIsInvalidWhenItIsExpired(): void
    {
        $smsCode = new SmsCodeDto();
        $expectedCode = $smsCode->code;
        $smsCode->expiration->modify('- 5 minutes');

        $session = $this->createMock(SessionInterface::class);
        $session->method('get')->with('sms_code')->willReturn($smsCode);

        $this->requestStack->method('getSession')->willReturn($session);

        self::assertFalse($this->codeGenerator->validateCode($expectedCode));
    }

    private function validateSmsCode(string $code): bool
    {
        return 6 === strlen($code) && is_numeric($code);
    }
}
