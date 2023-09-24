<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Settings\Config;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Debug\TraceableAuthenticator;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

#[AsEventListener]
class CheckPassportListener
{
    public function __construct(private readonly Config $config)
    {
    }

    public function __invoke(CheckPassportEvent $event): void
    {
        $auth = $event->getAuthenticator();

        if ($auth instanceof TraceableAuthenticator) {
            $auth = $auth->getAuthenticator();
        }

        if (($auth instanceof OAuth2Authenticator) && !$this->config->getFeatures()->isSSOEnabled()) {
            throw new CustomUserMessageAuthenticationException('SSO is not enabled');
        }

        if (($auth instanceof FormLoginAuthenticator) && $this->config->getSettings()->isPasswordLoginDisabled()) {
            throw new CustomUserMessageAuthenticationException('Password login is disabled');
        }
    }
}
