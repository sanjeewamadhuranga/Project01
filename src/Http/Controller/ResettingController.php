<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\Security\PasswordReset;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Form\Security\SetPasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

#[Route('/forgot', name: 'password_reset_')]
class ResettingController extends BaseController
{
    private const RESET_TOKEN_TTL = 3600;

    #[Route('/request', name: 'request')]
    public function request(Request $request, PasswordReset $passwordReset): Response
    {
        $email = $request->get('email');

        if (null !== $email) {
            try {
                $passwordReset->sendPasswordResetEmail($email);
            } catch (CustomUserMessageAccountStatusException) {
                $this->addFlash('danger', $this->trans('security.account_suspended'));

                return $this->render('security/password_reset/request.html.twig');
            }

            return $this->render('security/password_reset/check_email.html.twig', ['email' => $email]);
        }

        return $this->render('security/password_reset/request.html.twig');
    }

    #[Route('/reset/{token}', name: 'reset')]
    public function reset(Request $request, string $token, PasswordReset $passwordReset): Response
    {
        $user = $passwordReset->getUserByToken($token);

        if (!$user instanceof Administrator || $user->isDeleted() || $user->isPasswordRequestExpired(self::RESET_TOKEN_TTL)) {
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(SetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !$user->isSuspended()) {
            $passwordReset->resetPassword($user, $form->get('plainPassword')->getData());

            return $this->redirectToRoute('login');
        }

        if ($user->isSuspended()) {
            $this->addFlash('danger', 'This account is suspended, you can not reset password.');
        }

        return $this->render('security/password_reset/reset.html.twig', ['resetForm' => $form->createView()]);
    }
}
