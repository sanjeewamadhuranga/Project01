<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\Validator;

use App\Application\Security\Voter\ProtectedByRoleVoter;
use App\Application\Security\Voter\WildcardPermissionVoter;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Validator\ProtectedRole;
use App\Infrastructure\Validator\ProtectedRoleValidator;
use App\Tests\Feature\Traits\CreateTokenWithUserAndRoleTrait;
use App\Tests\Unit\UnitTestCase;
use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;

use function PHPUnit\Framework\never;
use function PHPUnit\Framework\once;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProtectedRoleValidatorTest extends UnitTestCase
{
    use CreateTokenWithUserAndRoleTrait;

    private TokenStorageInterface&Stub $tokenStorage;

    private ProtectedRoleValidator $validator;

    private ProtectedRole $constraint;

    private ExecutionContextInterface&MockObject $context;

    public function testItBuildsViolationWhenUserAddsProtectedRole(): void
    {
        $this->tokenStorage->method('getToken')->willReturn($this->getTokenWithUser('ROLE_ORDINARY_USER'));

        $roleName = 'NewRole';
        $role = $this->getRole($roleName, ['*']);
        $role->setProtectedByRole(new ManagerPortalRole());

        $this->context->expects(once())->method('buildViolation')->with(sprintf($this->constraint->message, $roleName));
        $this->validator->initialize($this->context);

        $this->validator->validate(new ArrayCollection([$role]), $this->constraint);
    }

    public function testItDoesNotBuildsViolationWhenUserAddsNotProtectedRole(): void
    {
        $this->tokenStorage->method('getToken')->willReturn($this->getTokenWithUser('ROLE_USER'));

        $roleName = 'NotProtectedRole';
        $role = $this->getRole($roleName, ['*']);

        $this->context->expects(never())->method('buildViolation');
        $this->validator->initialize($this->context);

        $this->validator->validate(new ArrayCollection([$role]), $this->constraint);
    }

    public function testItDoesNotBuildsViolationWhenUserWithProtectedRoleAddsProtectedRole(): void
    {
        $roleWhichProtects = new ManagerPortalRole();

        $user = $this->getUserWithRole('ROLE_ANOTHER_USER');
        $user->addManagerPortalRole($roleWhichProtects);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage->method('getToken')->willReturn($token);

        $roleName = 'NewRole';
        $role = $this->getRole($roleName, ['*']);
        $role->setProtectedByRole($roleWhichProtects);

        $this->context->expects(never())->method('buildViolation');
        $this->validator->initialize($this->context);

        $this->validator->validate(new ArrayCollection([$role]), $this->constraint);
    }

    protected function setUp(): void
    {
        $decisionManager = new AccessDecisionManager(new ArrayIterator(
            [
                new WildcardPermissionVoter(),
                new ProtectedByRoleVoter(),
            ]
        ));

        $this->tokenStorage = $this->createStub(TokenStorageInterface::class);
        $this->validator = new ProtectedRoleValidator(new AuthorizationChecker($this->tokenStorage, $decisionManager));
        $this->constraint = new ProtectedRole();
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }
}
