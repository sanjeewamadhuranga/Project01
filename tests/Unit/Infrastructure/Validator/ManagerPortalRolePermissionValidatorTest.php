<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Validator;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Validator\ManagerPortalRolePermission;
use App\Infrastructure\Validator\ManagerPortalRolePermissionValidator;
use App\Tests\Feature\Traits\DocumentManagerAwareTrait;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<ManagerPortalRolePermissionValidator>
 */
class ManagerPortalRolePermissionValidatorTest extends ConstraintValidatorTestCase
{
    use DocumentManagerAwareTrait;

    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private ManagerPortalRolePermission $managerPortalRolePermission;

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->managerPortalRolePermission = new ManagerPortalRolePermission();

        $this->initializeDocumentManager();

        parent::setUp();
    }

    public function testItDoesNotBuildValidationWhenUserHasAllPermissions(): void
    {
        $this->authorizationChecker->method('isGranted')->with('all_permissions')->willReturn(true);

        $this->validator->validate('TEST', $this->managerPortalRolePermission);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider permissionsNotFromAdministratorPermissions
     */
    public function testItDoesNotBuildViolationWhenPermissionIsGranted(string $permission): void
    {
        $this->authorizationChecker->method('isGranted')
            ->withConsecutive(['all_permissions'], ['EDIT_ROLE_PERMISSIONS'])
            ->willReturnOnConsecutiveCalls(false, true);

        $this->validator->validate($this->getManagerPortalRole($permission), $this->managerPortalRolePermission);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider permissionsFromAdministratorPermissions
     */
    public function testItBuildsViolationWhenUserHasNotAllPermissionsAndPermissionIsNotGranted(string $permission): void
    {
        $this->authorizationChecker->method('isGranted')->willReturn(false);

        $this->validator->validate($this->getManagerPortalRole($permission), $this->managerPortalRolePermission);

        $this->buildViolation(sprintf($this->managerPortalRolePermission->message, $permission))->assertRaised();
    }

    /**
     * @return iterable<string, string[]>
     */
    public function permissionsNotFromAdministratorPermissions(): iterable
    {
        yield 'TEST_PERMISSION' => ['TEST_PERMISSION'];
        yield 'TEST_PERMISSION_2' => ['TEST_PERMISSION_2'];
        yield 'COMPLIANCE_CASE CREATE' => [Permission::COMPLIANCE_CASE.Action::CREATE];
        yield 'COMPLIANCE_CASE VIEW' => [Permission::COMPLIANCE_CASE.Action::VIEW];
        yield 'COMPLIANCE_CASE EDIT' => [Permission::COMPLIANCE_CASE.Action::EDIT];
        yield 'COMPLIANCE_CASE DELETE' => [Permission::COMPLIANCE_CASE.Action::DELETE];
        yield 'MODULE_MERCHANT CREATE' => [Permission::MODULE_MERCHANT.Action::CREATE];
        yield 'MODULE_MERCHANT VIEW' => [Permission::MODULE_MERCHANT.Action::VIEW];
        yield 'MODULE_MERCHANT EDIT' => [Permission::MODULE_MERCHANT.Action::EDIT];
        yield 'MODULE_MERCHANT DELETE' => [Permission::MODULE_MERCHANT.Action::DELETE];
        yield 'MODULE_TRANSACTION CREATE' => [Permission::MODULE_TRANSACTION.Action::CREATE];
        yield 'MODULE_TRANSACTION VIEW' => [Permission::MODULE_TRANSACTION.Action::VIEW];
        yield 'MODULE_TRANSACTION EDIT' => [Permission::MODULE_TRANSACTION.Action::EDIT];
        yield 'MODULE_TRANSACTION DELETE' => [Permission::MODULE_TRANSACTION.Action::DELETE];
    }

    /**
     * @return iterable<string, string[]>
     */
    public function permissionsFromAdministratorPermissions(): iterable
    {
        yield 'Action::ANY' => [Action::ANY];
        yield 'Permission::MODULE_ADMINISTRATORS.Action::DISABLE' => [Permission::MODULE_ADMINISTRATORS.Action::DISABLE];
        yield 'Permission::MODULE_ADMINISTRATORS.Action::DOWNLOAD' => [Permission::MODULE_ADMINISTRATORS.Action::DOWNLOAD];
        yield 'Permission::MODULE_ADMINISTRATORS.Action::ENABLE' => [Permission::MODULE_ADMINISTRATORS.Action::ENABLE];
        yield 'Permission::MODULE_ADMINISTRATORS.Action::VIEW' => [Permission::MODULE_ADMINISTRATORS.Action::VIEW];
        yield 'Permission::MODULE_ADMINISTRATORS.Action::CREATE' => [Permission::MODULE_ADMINISTRATORS.Action::CREATE];
        yield 'Permission::MODULE_ADMINISTRATORS.Action::EDIT' => [Permission::MODULE_ADMINISTRATORS.Action::EDIT];
        yield 'Permission::MODULE_ADMINISTRATORS.Action::DELETE' => [Permission::MODULE_ADMINISTRATORS.Action::DELETE];
        yield 'Permission::MODULE_ADMINISTRATORS.Action::ANY' => [Permission::MODULE_ADMINISTRATORS.Action::ANY];
    }

    protected function createValidator(): ManagerPortalRolePermissionValidator
    {
        return new ManagerPortalRolePermissionValidator($this->authorizationChecker, $this->documentManager);
    }

    private function getManagerPortalRole(string $permission): ManagerPortalRole
    {
        $role = new ManagerPortalRole();
        $role->setNewPermissions([$permission]);

        return $role;
    }
}
