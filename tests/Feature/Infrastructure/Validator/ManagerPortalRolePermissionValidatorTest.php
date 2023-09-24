<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\Validator;

use App\Application\Security\Voter\ManagerPortalRolePermissionVoter;
use App\Application\Security\Voter\ProtectedByRoleVoter;
use App\Application\Security\Voter\WildcardPermissionVoter;
use App\Infrastructure\Validator\ManagerPortalRolePermission;
use App\Infrastructure\Validator\ManagerPortalRolePermissionValidator;
use App\Tests\Feature\Traits\CreateTokenWithUserAndRoleTrait;
use App\Tests\Feature\Traits\DocumentManagerAwareTrait;
use App\Tests\Unit\UnitTestCase;
use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ManagerPortalRolePermissionValidatorTest extends UnitTestCase
{
    use CreateTokenWithUserAndRoleTrait;
    use DocumentManagerAwareTrait;

    private TokenStorageInterface&Stub $tokenStorage;

    private ManagerPortalRolePermissionValidator $validator;

    private ManagerPortalRolePermission $constraint;

    private ExecutionContextInterface&MockObject $context;

    public function testItBuildsViolationWhenUserDoNotHaveAllPermissionsAndAddsProtectedPermission(): void
    {
        $this->tokenStorage->method('getToken')->willReturn($this->getTokenWithUser('ROLE_USER'));

        $addedPermission = '*';
        $this->context->expects(self::once())->method('buildViolation')->with(sprintf($this->constraint->message, $addedPermission));

        $this->validator->initialize($this->context);

        $this->validator->validate($this->getRole('SOME_ROLE', [$addedPermission]), $this->constraint);
    }

    public function testItDoesNotBuildViolationWhenUserHaveAllPermissionsAndAddsProtectedPermission(): void
    {
        $this->tokenStorage->method('getToken')->willReturn($this->getTokenWithUser('ROLE_ADMIN', ['*']));

        $addedPermission = '*';
        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->initialize($this->context);

        $this->validator->validate($this->getRole('OTHER_ROLE', [$addedPermission]), $this->constraint);
    }

    public function testItDoesNotBuildViolationWhenUserDoNotHaveAllPermissionsAndAddsNotProtectedPermission(): void
    {
        $this->tokenStorage->method('getToken')->willReturn($this->getTokenWithUser('ROLE_USER'));

        $addedPermission = 'some.unprotected';
        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->initialize($this->context);

        $this->validator->validate($this->getRole('ANOTHER_ROLE', [$addedPermission]), $this->constraint);
    }

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createStub(TokenStorageInterface::class);
        $wildcardPermissionVoter = new WildcardPermissionVoter();
        $protectedByRoleVoter = new ProtectedByRoleVoter();

        $decisionManager = new AccessDecisionManager(new ArrayIterator(
            [
                $wildcardPermissionVoter,
                $protectedByRoleVoter,
                new ManagerPortalRolePermissionVoter(new AuthorizationChecker($this->tokenStorage, new AccessDecisionManager(new ArrayIterator([
                    $wildcardPermissionVoter,
                    $protectedByRoleVoter,
                ])))),
            ]
        ));

        $this->initializeDocumentManager();
        $this->validator = new ManagerPortalRolePermissionValidator(new AuthorizationChecker($this->tokenStorage, $decisionManager), $this->documentManager);
        $this->constraint = new ManagerPortalRolePermission();
        $this->context = $this->createMock(ExecutionContextInterface::class);
    }
}
