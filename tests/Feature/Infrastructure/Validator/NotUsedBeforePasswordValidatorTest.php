<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\Validator;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Validator\NotUsedBeforePassword;
use App\Infrastructure\Validator\NotUsedBeforePasswordValidator;
use App\Tests\Feature\BaseTestCase;
use ReflectionClass;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PlaintextPasswordHasher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class NotUsedBeforePasswordValidatorTest extends BaseTestCase
{
    public function testYouCanNotUsePasswordFromPreviousPasswords(): void
    {
        $passwords = [uniqid(), uniqid(), uniqid(), uniqid(), uniqid()];
        $validator = $this->getValidator($passwords, new Administrator());

        foreach ($passwords as $password) {
            $validator->validate($password, new NotUsedBeforePassword());
        }
    }

    public function testYouCanUseSamePasswordAfterItIsRemovedFromPreviousPasswords(): void
    {
        $user = new Administrator();
        $password = 'firstPassword';

        $validator = $this->getValidator([$password], $user);
        $validator->validate($password, new NotUsedBeforePassword());

        $maxSavedPasswords = (new ReflectionClass(Administrator::class))->getConstant('NUMBER_OF_PREVIOUS_PASSWORDS_REMEMBERED');
        for ($i = 0; $i < $maxSavedPasswords; ++$i) {
            $user->addPreviousPassword(uniqid());
        }

        $validator = $this->getValidator([], $user);
        $validator->validate($password, new NotUsedBeforePassword());
    }

    /**
     * @param array<int, string> $previousPasswords
     */
    private function getTokenStorageWithUserWithPreviousPasswords(array $previousPasswords, Administrator $user): TokenStorageInterface
    {
        foreach ($previousPasswords as $password) {
            $user->addPreviousPassword($password);
        }

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $tokenStorage = $this->createStub(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        return $tokenStorage;
    }

    /**
     * @param array<int, string> $previousPasswords
     */
    private function getValidator(array $previousPasswords, Administrator $user): NotUsedBeforePasswordValidator
    {
        $constraint = new NotUsedBeforePassword();
        $passwordHasherFactory = new PasswordHasherFactory([PasswordAuthenticatedUserInterface::class => new PlaintextPasswordHasher()]);
        $tokenStorage = $this->getTokenStorageWithUserWithPreviousPasswords($previousPasswords, $user);
        $validator = new NotUsedBeforePasswordValidator($passwordHasherFactory, $tokenStorage);

        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraintViolationBuilder->expects(self::exactly(count($previousPasswords)))->method('addViolation');

        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext->expects(self::exactly(count($previousPasswords)))->method('buildViolation')->with($constraint->message)->willReturn($constraintViolationBuilder);
        $validator->initialize($executionContext);

        return $validator;
    }
}
