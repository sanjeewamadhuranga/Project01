<?php

declare(strict_types=1);

namespace App\Application\Security\TwoFactor\Provider;

use App\Application\Security\TwoFactor\Generator\SmsCodeGenerator;
use App\Domain\Document\Security\Administrator;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\DefaultTwoFactorFormRenderer;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Twig\Environment;

#[AutoconfigureTag('scheb_two_factor.provider', ['alias' => 'sms'])]
class SmsTwoFactorProvider implements TwoFactorProviderInterface
{
    public function __construct(private readonly SmsCodeGenerator $codeGenerator, private readonly Environment $twigEnvironment)
    {
    }

    public function beginAuthentication(AuthenticationContextInterface $context): bool
    {
        $user = $context->getUser();

        return $user instanceof Administrator && $user->isSmsAuthenticationEnabled();
    }

    public function prepareAuthentication(object $user): void
    {
        if (!$user instanceof Administrator) {
            return;
        }

        $this->codeGenerator->generateAndSend($user);
    }

    public function validateAuthenticationCode(object $user, string $authenticationCode): bool
    {
        if (!$user instanceof Administrator) {
            return false;
        }

        return $this->codeGenerator->validateCode($authenticationCode);
    }

    public function getFormRenderer(): TwoFactorFormRendererInterface
    {
        return new DefaultTwoFactorFormRenderer($this->twigEnvironment, 'security/2fa_form.html.twig');
    }
}
