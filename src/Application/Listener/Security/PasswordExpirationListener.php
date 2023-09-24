<?php

declare(strict_types=1);

namespace App\Application\Listener\Security;

use App\Application\Listener\RouteCheckingListener;
use App\Domain\Document\Security\Administrator;
use App\Domain\Settings\SystemSettings;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\HttpUtils;

#[AsEventListener]
class PasswordExpirationListener extends RouteCheckingListener
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly HttpUtils $httpUtils,
        private readonly SystemSettings $settings
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if ($this->settings->isPasswordLoginDisabled()) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token instanceof UsernamePasswordToken && !$token instanceof RememberMeToken) {
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof Administrator) {
            return;
        }

        if (!$user->isPasswordExpired()) {
            return;
        }

        if ($this->isAllowedRoute($event->getRequest())) {
            return;
        }

        $event->setResponse($this->httpUtils->createRedirectResponse($event->getRequest(), 'profile_change_password'));
    }
}
