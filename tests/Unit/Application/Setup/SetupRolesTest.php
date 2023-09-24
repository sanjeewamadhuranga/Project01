<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Setup;

use App\Application\Setup\SetupRoles;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Infrastructure\Repository\Security\ManagerPortalRoleRepository;
use App\Tests\Unit\UnitTestCase;

use function PHPUnit\Framework\exactly;

use PHPUnit\Framework\MockObject\MockObject;

use function PHPUnit\Framework\never;

class SetupRolesTest extends UnitTestCase
{
    private ManagerPortalRoleRepository&MockObject $roleRepository;

    private SetupRoles $setupRoles;

    protected function setUp(): void
    {
        $this->roleRepository = $this->createMock(ManagerPortalRoleRepository::class);

        $this->setupRoles = new SetupRoles($this->roleRepository);
    }

    public function testItAddsTwoAdminRolesWhenThereAreNoRoles(): void
    {
        $this->roleRepository->method('countAll')->willReturn(0);

        $this->roleRepository->expects(exactly(2))->method('save')->withConsecutive(
            [self::callback(static fn ($role) => $role instanceof ManagerPortalRole && ['*'] === $role->getNewPermissions() && 'ROLE__ADMIN' === $role->getName())],
            [self::callback(static fn ($role) => $role instanceof ManagerPortalRole && ['*'] === $role->getNewPermissions() && 'ROLE_SUPER_ADMIN' === $role->getName())],
        );

        $this->setupRoles->create();
    }

    public function testItDoNothingWhenThereAreSomeRoles(): void
    {
        $this->roleRepository->method('countAll')->willReturn(1);

        $this->roleRepository->expects(never())->method('save');

        $this->setupRoles->create();
    }
}
