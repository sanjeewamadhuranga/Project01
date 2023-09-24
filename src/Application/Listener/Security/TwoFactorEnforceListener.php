<?php

declare(strict_types=1);

namespace App\Application\Listener\Security;

use App\Application\Listener\RouteCheckingListener;
use App\Domain\Document\Security\Administrator;
use App\Domain\Settings\Features;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\HttpUtils;

#[AsEventListener]
class TwoFactorEnforceListener extends RouteCheckingListener
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly HttpUtils $httpUtils,
        private readonly Features $features
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$this->features->is2FAEnforced() || !$this->features->is2FAEnabled()) {
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

        if ($user->is2FaEnabled()) {
            return;
        }

        if ($this->isAllowedRoute($event->getRequest())) {
            return;
        }

        $event->setResponse($this->httpUtils->createRedirectResponse($event->getRequest(), 'profile_index'));
    }

    public function isAllowedRoute(Request $request): bool
    {
        if (str_starts_with($request->attributes->get('_route'), 'profile_')) {
            return true;
        }

        return parent::isAllowedRoute($request);
    }

    protected function getAllowedRoutes(): array
    {
        return array_merge(parent::getAllowedRoutes(), ['logout', '2fa_login', '2fa_login_check', '2fa_resend_sms']);
    }
}
