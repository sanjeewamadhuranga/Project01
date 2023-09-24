<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\Locale\LocaleSetter;
use App\Application\Security\TwoFactor\Generator\SmsCodeGenerator;
use App\Domain\Document\Security\Administrator;
use App\Domain\Event\User\TwoFactorDisableEvent;
use App\Domain\Event\User\TwoFactorSetupEvent;
use App\Domain\Settings\Features;
use App\Domain\Settings\SystemSettings;
use App\Http\Model\Request\ChangePasswordRequest;
use App\Http\Model\Request\EnableTwoFactorRequest;
use App\Http\Model\Request\PasswordConfirmationRequest;
use App\Infrastructure\Form\Security\ChangePasswordFormType;
use App\Infrastructure\Form\Security\EnableTwoFactorAuthType;
use App\Infrastructure\Form\Security\PasswordConfirmationType;
use App\Infrastructure\Form\Security\UserPhoneNumberType;
use App\Infrastructure\Form\Security\UserProfileType;
use App\Infrastructure\Http\Attribute\RequireFeature;
use App\Infrastructure\Repository\Security\UserRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: 'profile_')]
class ProfileController extends BaseController
{
    public function __construct(private readonly SystemSettings $systemSettings, private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    #[Route('', name: 'index')]
    public function index(Request $request, UserRepository $repository, LocaleSetter $localeSetter): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($user);
            if (null !== $user->getLocale()) {
                $localeSetter->setLocale($request, $user->getLocale());
                $this->container->get('translator')->setLocale($request->getLocale());
            }
            $this->addFlash('success', $this->trans('administrators.profileUpdated'));
        }

        return $this->renderForm('profile/index.html.twig', ['form' => $form]);
    }

    #[Route('/2fa/get-app', name: '2fa_get_app')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function getApp(): Response
    {
        return $this->renderForm('profile/get_app.html.twig');
    }

    #[Route('/2fa/enable-app', name: '2fa_enable_app')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function enable2faApp(
        GoogleAuthenticatorInterface $googleAuthenticator,
        UserRepository $userRepository,
        Request $request,
        SessionInterface $session,
    ): Response {
        $user = $this->getUser();
        if ($user->isGoogleAuthenticatorEnabled()) {
            $this->addFlash('danger', $this->trans('security.2fa.message.error'));

            return $this->redirectToRoute('profile_index');
        }
        if ($this->systemSettings->isPasswordLoginDisabled()) {
            $this->addFlash('danger', $this->trans('security.2fa.message.2faNotEnabled'));

            return $this->redirectToRoute('profile_index');
        }

        $enableRequest = new EnableTwoFactorRequest();
        $form = $this->createForm(EnableTwoFactorAuthType::class, $enableRequest)
            ->handleRequest($request);

        $secret = $session->get('2faSecret');
        if (!$form->isSubmitted()) {
            $secret = $googleAuthenticator->generateSecret();
            $session->set('2faSecret', $secret);
        }
        $user->setGoogleAuthenticatorSecret($secret);

        if ($form->isSubmitted()) {
            if (!$googleAuthenticator->checkCode($user, $enableRequest->code)) {
                $form->get('code')->addError(new FormError($this->trans('security.2fa.invalidCode')));
            }

            if ($form->isValid()) {
                $session->remove('2faSecret');
                $userRepository->save($user);

                // call event
                $this->eventDispatcher->dispatch(new TwoFactorSetupEvent($user, Administrator::MFA_GOOGLE));

                return $this->renderForm('profile/enable2fa_app.html.twig', [
                    'isSuccess' => true,
                ]);
            }
        }

        return $this->renderForm('profile/enable2fa_app.html.twig', [
            'qrContent' => $googleAuthenticator->getQRContent($user),
            'form' => $form,
            'isSuccess' => false,
        ]);
    }

    #[Route('/2fa/disable-app', name: '2fa_disable_app')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function disable2faApp(UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordConfirmationType::class, new PasswordConfirmationRequest($user))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setGoogleAuthenticatorSecret(null);
            $userRepository->save($user);
            $this->eventDispatcher->dispatch(new TwoFactorDisableEvent($user, Administrator::MFA_GOOGLE));

            $this->addFlash('success', $this->trans('security.2fa.message.success'));

            return $this->redirectToRoute('profile_index');
        }

        return $this->renderForm('security/password_confirmation.html.twig', [
            'form' => $form,
            'backUrl' => 'profile_2fa_security',
        ]);
    }

    #[Route('/2fa/get-sms', name: '2fa_get_sms')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function getSms(Request $request, SessionInterface $session, SmsCodeGenerator $codeGenerator): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPhoneNumberType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('phone_number', $user->getPhoneNumber());
            $codeGenerator->generateAndSend($user);

            return $this->redirectToRoute('profile_2fa_enable_sms');
        }

        return $this->renderForm('profile/get_sms.html.twig', ['form' => $form]);
    }

    #[Route('/2fa/enable-sms', name: '2fa_enable_sms')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function enable2faSms(Request $request, SessionInterface $session, SmsCodeGenerator $codeGenerator, UserRepository $userRepository): Response
    {
        $enableRequest = new EnableTwoFactorRequest();
        $form = $this->createForm(EnableTwoFactorAuthType::class, $enableRequest)->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$codeGenerator->validateCode($enableRequest->code)) {
                $form->get('code')->addError(new FormError($this->trans('security.2fa.invalidCode')));
            }

            if ($form->isValid()) {
                $user = $this->getUser();
                $phoneNumber = $session->get('phone_number');
                $user->setPhoneNumber($phoneNumber);
                $user->setIsSmsAuthenticationEnabled(true);
                $userRepository->save($user);
                $this->eventDispatcher->dispatch(new TwoFactorSetupEvent($user, Administrator::MFA_SMS));

                $session->remove('sms_code');
                $session->remove('phone_number');

                return $this->renderForm('profile/enable2fa_sms.html.twig', ['isSuccess' => true]);
            }
        }

        return $this->renderForm('profile/enable2fa_sms.html.twig', [
            'form' => $form,
            'isSuccess' => false,
        ]);
    }

    #[Route('/2fa/disable-sms', name: '2fa_disable_sms')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function disable2faSms(UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordConfirmationType::class, new PasswordConfirmationRequest($user))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsSmsAuthenticationEnabled(false);
            $userRepository->save($user);
            $this->eventDispatcher->dispatch(new TwoFactorDisableEvent($user, Administrator::MFA_SMS));

            $this->addFlash('success', $this->trans('security.2fa.message.success'));

            return $this->redirectToRoute('profile_index');
        }

        return $this->renderForm('security/password_confirmation.html.twig', [
            'form' => $form,
            'backUrl' => 'profile_2fa_security',
        ]);
    }

    #[Route('/2fa/security', name: '2fa_security')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function security(): Response
    {
        return $this->renderForm('profile/security.html.twig');
    }

    #[Route('/2fa/disable', name: '2fa_disable')]
    #[RequireFeature(Features::LOGIN_2FA)]
    public function disable2fa(UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(PasswordConfirmationType::class, new PasswordConfirmationRequest($user))->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->reset2fa();
            $userRepository->save($user);
            $this->eventDispatcher->dispatch(new TwoFactorDisableEvent($user, Administrator::MFA_SMS));
            $this->eventDispatcher->dispatch(new TwoFactorDisableEvent($user, Administrator::MFA_GOOGLE));

            $this->addFlash('success', $this->trans('security.2fa.message.success'));

            return $this->redirectToRoute('profile_index');
        }

        return $this->renderForm('security/password_confirmation.html.twig', [
            'form' => $form,
            'backUrl' => 'profile_2fa_security',
        ]);
    }

    #[Route('/change-password', name: 'change_password')]
    public function changePassword(Request $request, UserRepository $userRepository, SystemSettings $settings): Response
    {
        if ($settings->isPasswordLoginDisabled()) {
            $this->addFlash('danger', $this->trans('security.password_login_disabled'));

            return $this->redirectToRoute('profile_index');
        }

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, new ChangePasswordRequest())
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $user->getPassword()) {
                $user->addPreviousPassword($user->getPassword());
            }
            $user->setPlainPassword($form->getData()->newPassword);
            $user->updatePasswordExpiration();
            $user->setPassword(uniqid('', true));
            $userRepository->save($user);
            $this->addFlash('success', $this->trans('security.message.success'));

            return $this->redirectToRoute('profile_index');
        }

        return $this->renderForm('profile/change_password.html.twig', ['form' => $form]);
    }
}
