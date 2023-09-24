<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security\Voter;

use App\Application\Security\Voter\ProtectedByRoleVoter;
use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Security\ManagerPortalRole;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProtectedByRoleVoterTest extends UnitTestCase
{
    private ProtectedByRoleVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new ProtectedByRoleVoter();

        parent::setUp();
    }

    public function testItDeniesAccessToInvalidUser(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($this->createStub(UserInterface::class));

        self::assertSame(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, $this->createStub(ManagerPortalRole::class), ['PROTECT_ROLE_BY_OTHER_ROLE']));
    }

    public function testItGrantsAccessToRoleWhichIsNotProtected(): void
    {
        self::assertSame(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->getToken(), new ManagerPortalRole(), ['PROTECT_ROLE_BY_OTHER_ROLE']));
    }

    public function testItGrantsAccessWhenUserHasSufficientPermissions(): void
    {
        $protectedByRole = $this->createStub(ManagerPortalRole::class);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $this->getToken($protectedByRole),
                $this->getRoleProtectedByOtherRole($protectedByRole),
                ['PROTECT_ROLE_BY_OTHER_ROLE']
            )
        );
    }

    public function testItDeniesAccessWhenUserDontHavePermission(): void
    {
        $protectedByRole = $this->createStub(ManagerPortalRole::class);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $this->getToken(),
                $this->getRoleProtectedByOtherRole($protectedByRole),
                ['PROTECT_ROLE_BY_OTHER_ROLE']
            )
        );
    }

    private function getRoleProtectedByOtherRole(ManagerPortalRole $protectedByRole): ManagerPortalRole
    {
        $managerPortalRole = new ManagerPortalRole();
        $managerPortalRole->setProtectedByRole($protectedByRole);

        return $managerPortalRole;
    }

    private function getToken(ManagerPortalRole $role = null): TokenInterface
    {
        $user = new Administrator();
        if (null !== $role) {
            $user->addManagerPortalRole($role);
        }

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
