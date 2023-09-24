<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Validator;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Validator\CurrentUserPassword;
use App\Infrastructure\Validator\CurrentUserPasswordValidator;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PlaintextPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<CurrentUserPasswordValidator>
 */
class CurrentUserPasswordValidatorTest extends ConstraintValidatorTestCase
{
    public function testItBuildsViolationWhenNotProperPasswordProvided(): void
    {
        $password = 'someOtherPassword777';
        $constraint = new CurrentUserPassword();

        $this->validator->validate($password, $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    public function testItDoesNotBuildsViolationWhenProperPasswordProvided(): void
    {
        $password = 'currentPassword666';
        $constraint = new CurrentUserPassword();

        $this->validator->validate($password, $constraint);

        $this->assertNoViolation();
    }

    protected function createValidator(): CurrentUserPasswordValidator
    {
        $user = $this->createStub(Administrator::class);
        $user->method('getPassword')->willReturn('currentPassword666');

        $passwordHasher = new UserPasswordHasher(new PasswordHasherFactory([PasswordAuthenticatedUserInterface::class => new PlaintextPasswordHasher()]));

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $tokenStorage = $this->createStub(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        return new CurrentUserPasswordValidator($passwordHasher, $tokenStorage);
    }
}
