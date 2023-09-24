<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\Security\UserRepository;
use DateTimeImmutable;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class PasswordReset
{
    public function __construct(private readonly UserRepository $userRepository, private readonly TokenGenerator $tokenGenerator, private readonly MailerInterface $mailer)
    {
    }

    public function sendPasswordResetEmail(string $email): void
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user instanceof Administrator) {
            return;
        }

        if ($user->isDeleted()) {
            throw new CustomUserMessageAccountStatusException('This account is deleted!');
        }

        if ($user->isSuspended()) {
            throw new CustomUserMessageAccountStatusException('This account is suspended!');
        }

        $user->setConfirmationToken($this->tokenGenerator->generateToken());

        $user->setPasswordRequestedAt(new DateTimeImmutable());
        $this->userRepository->save($user);

        $message = (new TemplatedEmail())
            ->to($user->getEmailCanonical())
            ->subject('Reset Password')
            ->htmlTemplate('security/password_reset/email.html.twig')
            ->context(['user' => $user])
        ;

        $this->mailer->send($message);
    }

    public function getUserByToken(string $token): ?Administrator
    {
        return $this->userRepository->findOneBy(['confirmationToken' => $token]);
    }

    public function resetPassword(Administrator $user, string $password): void
    {
        $user->setPlainPassword($password);
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->updatePasswordExpiration();

        $this->userRepository->save($user);
    }
}
