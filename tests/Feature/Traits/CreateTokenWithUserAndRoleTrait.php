<?php

declare(strict_types=1);

namespace App\Tests\Feature\Traits;

use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

trait CreateTokenWithUserAndRoleTrait
{
    /**
     * @param string[] $rolePermissions
     */
    private function getUserWithRole(string $roleName, array $rolePermissions = []): Administrator
    {
        $user = new Administrator();
        $user->setUsername(uniqid());
        $user->addManagerPortalRole($this->getRole($roleName, $rolePermissions));

        return $user;
    }

    /**
     * @param string[] $permissions
     */
    private function getRole(string $name, array $permissions): ManagerPortalRole
    {
        $role = new ManagerPortalRole();
        $role->setName($name);
        $role->setNewPermissions($permissions);

        return $role;
    }

    /**
     * @param string[] $rolePermissions
     */
    private function getTokenWithUser(string $roleName, array $rolePermissions = []): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($this->getUserWithRole($roleName, $rolePermissions));

        return $token;
    }
}
