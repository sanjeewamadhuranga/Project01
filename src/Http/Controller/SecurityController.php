<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\Security\TwoFactor\Generator\SmsCodeGenerator;
use App\Application\Setup\AdministratorAccountSetupWizardDetector;
use App\Domain\Settings\Features;
use App\Infrastructure\Http\Attribute\RequireFeature;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils, TokenStorageInterface $tokenStorage, AdministratorAccountSetupWizardDetector $wizardDetector, Request $request): Response
    {
        if (null !== $tokenStorage->getToken()?->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = '' !== $authenticationUtils->getLastUsername() ? $authenticationUtils->getLastUsername() : $request->cookies->get('last_username');

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'needSetup' => $wizardDetector->needSetup()]);
    }

    #[Route('/2fa/resend-sms', name: '2fa_resend_sms')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function resendSmsCode(SmsCodeGenerator $codeGenerator): Response
    {
        $user = $this->getUser();
        $codeGenerator->generateAndSend($user);

        return $this->redirectToRoute('2fa_login', ['preferProvider' => 'sms_two_factor_provider']);
    }

    #[Route('/connect/google', name: 'connect_google')]
    public function connectGoogle(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry->getClient('google')->redirect([], []);
    }
}
