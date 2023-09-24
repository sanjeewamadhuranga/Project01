<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Security\Voter;

use App\Application\Security\Voter\ManagerPortalRolePermissionVoter;
use App\Application\Security\Voter\WildcardPermissionVoter;
use App\Tests\Feature\Traits\CreateTokenWithUserAndRoleTrait;
use App\Tests\Unit\UnitTestCase;
use ArrayIterator;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ManagerPortalRolePermissionVoterTest extends UnitTestCase
{
    use CreateTokenWithUserAndRoleTrait;

    private TokenStorageInterface&Stub $tokenStorage;

    private ManagerPortalRolePermissionVoter $voter;

    private string $adminProtectedPermission = 'administrators.view';

    /**
     * @var string[]
     */
    private array $voterAttributes = ['EDIT_ROLE_PERMISSIONS'];

    public function testItDeniesAccessWhenUserDoNotHaveAllPermissions(): void
    {
        $userToken = $this->getTokenWithUser('ROLE_ORDINARY_USER');
        $this->tokenStorage->method('getToken')->willReturn($userToken);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote(
                $userToken,
                $this->adminProtectedPermission,
                $this->voterAttributes,
            )
        );
    }

    public function testItGrantsAccessWhenUserHaveAllPermissions(): void
    {
        $userToken = $this->getTokenWithUser('ROLE_ADMIN_USER', ['*']);
        $this->tokenStorage->method('getToken')->willReturn($userToken);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $userToken,
                $this->adminProtectedPermission,
                $this->voterAttributes,
            )
        );
    }

    public function testItGrantsAccessToNotSecuredPermissionForOrdinaryUser(): void
    {
        $userToken = $this->getTokenWithUser('ROLE_ORDINARY_USER');
        $this->tokenStorage->method('getToken')->willReturn($userToken);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote(
                $userToken,
                'some_not_administration_permission',
                $this->voterAttributes,
            )
        );
    }

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createStub(TokenStorageInterface::class);

        $decisionManager = new AccessDecisionManager(new ArrayIterator([new WildcardPermissionVoter()]));

        $this->voter = new ManagerPortalRolePermissionVoter(new AuthorizationChecker($this->tokenStorage, $decisionManager));
    }
}
