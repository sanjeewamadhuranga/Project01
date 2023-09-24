<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Validator;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Validator\NotUsedBeforePassword;
use App\Infrastructure\Validator\NotUsedBeforePasswordValidator;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\PlaintextPasswordHasher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<NotUsedBeforePasswordValidator>
 */
class NotUsedBeforePasswordValidatorTest extends ConstraintValidatorTestCase
{
    public function testItBuildsViolationWhenUsedBeforePasswordProvided(): void
    {
        $password = 'firstPassword123';
        $constraint = new NotUsedBeforePassword();

        $this->validator->validate($password, $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    public function testItDoesNotBuildViolationWhenNotUsedBeforePasswordProvided(): void
    {
        $password = 'thirdPassword789';

        $this->validator->validate($password, new NotUsedBeforePassword());

        $this->assertNoViolation();
    }

    protected function createValidator(): NotUsedBeforePasswordValidator
    {
        $passwordHasher = new PlaintextPasswordHasher();

        $firstPassword = $passwordHasher->hash('firstPassword123');
        $secondPassword = $passwordHasher->hash('secondPassword456');

        $user = $this->createStub(Administrator::class);
        $user->method('getPreviousPasswords')->willReturn([$firstPassword, $secondPassword]);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $tokenStorageInterface = $this->createStub(TokenStorageInterface::class);
        $tokenStorageInterface->method('getToken')->willReturn($token);

        $passwordHasherFactory = $this->createStub(PasswordHasherFactoryInterface::class);
        $passwordHasherFactory->method('getPasswordHasher')->willReturn($passwordHasher);

        return new NotUsedBeforePasswordValidator($passwordHasherFactory, $tokenStorageInterface);
    }
}
