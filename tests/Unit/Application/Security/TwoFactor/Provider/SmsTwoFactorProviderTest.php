<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security\TwoFactor\Provider;

use App\Application\Security\TwoFactor\Generator\SmsCodeGenerator;
use App\Application\Security\TwoFactor\Provider\SmsTwoFactorProvider;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class SmsTwoFactorProviderTest extends UnitTestCase
{
    private SmsCodeGenerator&MockObject $codeGenerator;

    private SmsTwoFactorProvider $provider;

    protected function setUp(): void
    {
        $this->codeGenerator = $this->createMock(SmsCodeGenerator::class);

        $this->provider = new SmsTwoFactorProvider($this->codeGenerator, $this->createStub(Environment::class));
    }

    /**
     * @dataProvider userWithBeginAuthenticationResultProvider
     */
    public function testWillBeginAuthenticationWhenUserHasPhoneNumberAndEnabledSmsAuthentication(?string $phoneNumber, bool $isSmsAuthenticationEnabled, bool $result): void
    {
        $user = new Administrator();
        $user->setPhoneNumber($phoneNumber);
        $user->setIsSmsAuthenticationEnabled($isSmsAuthenticationEnabled);

        $context = $this->createStub(AuthenticationContextInterface::class);
        $context->method('getUser')->willReturn($user);

        self::assertSame($result, $this->provider->beginAuthentication($context));
    }

    public function testItDoNotGenerateAndSendCodeWhenIncorrectUserProvided(): void
    {
        $this->codeGenerator->expects(self::never())->method('generateAndSend');

        $this->provider->prepareAuthentication($this->createStub(UserInterface::class));
    }

    public function testItGeneratesAndSendsCodeWhenCorrectUserProvided(): void
    {
        $user = $this->createStub(Administrator::class);
        $this->codeGenerator->expects(self::once())->method('generateAndSend')->with($user);

        $this->provider->prepareAuthentication($user);
    }

    public function testItFailsToValidateCodeWhenIncorrectUserProvided(): void
    {
        self::assertFalse($this->provider->validateAuthenticationCode($this->createStub(UserInterface::class), '1234'));
    }

    public function testItValidatesCodeUsingCodeGeneratorWhenCorrectUserProvided(): void
    {
        $authenticationCode = '6789';

        $this->codeGenerator->expects(self::once())->method('validateCode')->with($authenticationCode);

        $this->provider->validateAuthenticationCode($this->createStub(Administrator::class), $authenticationCode);
    }

    public function testItUsesSpecificTemplateForRenderingForm(): void
    {
        $formRenderer = $this->provider->getFormRenderer();

        $reflectionProperty = (new ReflectionClass($formRenderer))->getProperty('template');
        $reflectionProperty->setAccessible(true);

        self::assertSame('security/2fa_form.html.twig', $reflectionProperty->getValue($formRenderer));
    }

    /**
     * @return iterable<string, array{string|null, bool, bool}>
     */
    public function userWithBeginAuthenticationResultProvider(): iterable
    {
        yield 'user with phone number and sms authentication enabled' => ['+47500500500', true, true];
        yield 'user without phone number and sms authentication enabled' => [null, true, false];
        yield 'user with phone number and sms authentication disabled' => ['+47500500500', false, false];
    }
}
