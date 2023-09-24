<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security;

use App\Application\Security\PasswordReset;
use App\Application\Security\TokenGenerator;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Tests\Unit\UnitTestCase;
use DateTimeInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class PasswordResetTest extends UnitTestCase
{
    public function testItSearchForUserByTokenInRepository(): void
    {
        $token = 'some-token';

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects(self::once())->method('findOneBy')->with(['confirmationToken' => $token]);

        $passwordReset = new PasswordReset($userRepository, $this->createStub(TokenGenerator::class), $this->createStub(MailerInterface::class));
        $passwordReset->getUserByToken($token);
    }

    public function testItSetsPlainPasswordResetsConfirmationTokenPasswordExpirationDateAndSavesUser(): void
    {
        $newPassword = 's0m3-n3w-p4ssw0rd';
        $user = $this->createMock(Administrator::class);
        $user->expects(self::once())->method('setPlainPassword')->with($newPassword);
        $user->expects(self::once())->method('setConfirmationToken')->with(null);
        $user->expects(self::once())->method('setPasswordRequestedAt')->with(null);
        $user->expects(self::once())->method('updatePasswordExpiration');

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects(self::once())->method('save')->with($user);

        $passwordReset = new PasswordReset($userRepository, $this->createStub(TokenGenerator::class), $this->createStub(MailerInterface::class));
        $passwordReset->resetPassword($user, $newPassword);
    }

    public function testItDoesNotSendMailWhenNoUserWithProvidedEmail(): void
    {
        $userRepository = $this->createStub(UserRepository::class);
        $userRepository->method('getUserByEmail')->willReturn(null);

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');

        $passwordReset = new PasswordReset($userRepository, $this->createStub(TokenGenerator::class), $this->createStub(MailerInterface::class));
        $passwordReset->sendPasswordResetEmail('email@pay.com');
    }

    public function testItThrowsExceptionWhenTryingToSendEmailToUserWhichIsSuspended(): void
    {
        $user = $this->createStub(Administrator::class);
        $user->method('isSuspended')->willReturn(true);

        $userRepository = $this->createStub(UserRepository::class);
        $userRepository->method('getUserByEmail')->willReturn($user);

        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('This account is suspended!');

        $passwordReset = new PasswordReset($userRepository, $this->createStub(TokenGenerator::class), $this->createStub(MailerInterface::class));
        $passwordReset->sendPasswordResetEmail('email@pay.com');
    }

    public function testItSendsPasswordResetEmail(): void
    {
        $userEmail = 'user@pay.com';
        $user = $this->createStub(Administrator::class);
        $user->method('isSuspended')->willReturn(false);
        $user->method('getEmailCanonical')->willReturn($userEmail);

        $userRepository = $this->createStub(UserRepository::class);
        $userRepository->method('getUserByEmail')->willReturn($user);

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())
            ->method('send')
            ->with(self::callback(static fn ($subject) => $subject instanceof TemplatedEmail && $userEmail === $subject->getTo()[0]->getAddress()));

        $passwordReset = new PasswordReset($userRepository, $this->createStub(TokenGenerator::class), $mailer);
        $passwordReset->sendPasswordResetEmail($userEmail);
    }

    public function testItSetsConfirmationTokenAndPasswordRequestedAtWhenSendingEmail(): void
    {
        $userEmail = 'user@pay.com';
        $token = 't0k3n';

        $tokenGenerator = $this->createMock(TokenGenerator::class);
        $tokenGenerator->expects(self::once())->method('generateToken')->willReturn($token);

        $user = $this->createMock(Administrator::class);
        $user->method('isSuspended')->willReturn(false);
        $user->method('getConfirmationToken')->willReturn(null);
        $user->method('getEmailCanonical')->willReturn($userEmail);
        $user->expects(self::once())->method('setConfirmationToken')->with($token);
        $user->expects(self::once())->method('setPasswordRequestedAt')->with(self::callback(static fn ($subject) => $subject instanceof DateTimeInterface));

        $userRepository = $this->createStub(UserRepository::class);
        $userRepository->method('getUserByEmail')->willReturn($user);

        $passwordReset = new PasswordReset($userRepository, $tokenGenerator, $this->createStub(MailerInterface::class));
        $passwordReset->sendPasswordResetEmail($userEmail);
    }
}
