<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security\Voter;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Application\Security\Voter\ManagerPortalRolePermissionVoter;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ManagerPortalRolePermissionVoterTest extends UnitTestCase
{
    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private ManagerPortalRolePermissionVoter $voter;

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->voter = new ManagerPortalRolePermissionVoter($this->authorizationChecker);

        parent::setUp();
    }

    public function testItDeniesAccessToInvalidUser(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($this->createStub(UserInterface::class));

        self::assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, 'TEST_PERMISSION', ['EDIT_ROLE_PERMISSIONS']));
    }

    public function testItGrantsAccessWhenUserHasAllPermissions(): void
    {
        $this->authorizationChecker->method('isGranted')->with('all_permissions')->willReturn(true);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->getToken(), 'TEST_PERMISSION_1', ['EDIT_ROLE_PERMISSIONS']));
    }

    /**
     * @dataProvider permissionAndExpectedResultProvider
     */
    public function testPermissionWithResult(int $access, string $permission): void
    {
        $this->authorizationChecker->method('isGranted')->with('all_permissions')->willReturn(false);

        self::assertSame($access, $this->voter->vote($this->getToken(), $permission, ['EDIT_ROLE_PERMISSIONS']));
    }

    private function getToken(): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($this->createStub(Administrator::class));

        return $token;
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    private function permissionAndExpectedResultProvider(): iterable
    {
        yield 'TEST_PERMISSION_3' => [VoterInterface::ACCESS_GRANTED, 'TEST_PERMISSION_3'];
        yield '*' => [VoterInterface::ACCESS_DENIED, Action::ANY];
        yield 'TEST_PERMISSION_4' => [VoterInterface::ACCESS_GRANTED, 'TEST_PERMISSION_4'];
        yield 'administrators.disable' => [VoterInterface::ACCESS_DENIED, Permission::MODULE_ADMINISTRATORS.Action::DISABLE];
        yield 'TEST_PERMISSION_5' => [VoterInterface::ACCESS_GRANTED, 'TEST_PERMISSION_5'];
        yield 'administrators.download' => [VoterInterface::ACCESS_DENIED, Permission::MODULE_ADMINISTRATORS.Action::DOWNLOAD];
        yield 'TEST_PERMISSION_6' => [VoterInterface::ACCESS_GRANTED, 'TEST_PERMISSION_6'];
        yield 'administrators.enable' => [VoterInterface::ACCESS_DENIED, Permission::MODULE_ADMINISTRATORS.Action::ENABLE];
        yield 'administrators.view' => [VoterInterface::ACCESS_DENIED, Permission::MODULE_ADMINISTRATORS.Action::VIEW];
        yield 'administrators.create' => [VoterInterface::ACCESS_DENIED, Permission::MODULE_ADMINISTRATORS.Action::CREATE];
        yield 'administrators.edit' => [VoterInterface::ACCESS_DENIED, Permission::MODULE_ADMINISTRATORS.Action::EDIT];
        yield 'administrators.delete' => [VoterInterface::ACCESS_DENIED, Permission::MODULE_ADMINISTRATORS.Action::DELETE];
        yield 'administrators.*' => [VoterInterface::ACCESS_DENIED, Permission::MODULE_ADMINISTRATORS.Action::ANY];
    }
}
