<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security\Voter;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Application\Security\Voter\WildcardPermissionVoter;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class WildcardPermissionVoterTest extends UnitTestCase
{
    /**
     * @dataProvider permissionDataProvider
     *
     * @param string[] $permissions
     */
    public function testItHandlesWildcardPermissions(string $attribute, array $permissions, int $result): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($this->getUser($permissions));

        $voter = new WildcardPermissionVoter();

        self::assertSame($result, $voter->vote($token, null, [$attribute]));
    }

    public function testItDeniesAccessToInvalidUserClass(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($this->createStub(UserInterface::class));

        $voter = new WildcardPermissionVoter();

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, null, ['test']));
    }

    /**
     * @return iterable<string, array{string, string[], int}>
     */
    public function permissionDataProvider(): iterable
    {
        yield 'Full match access' => [
            Permission::OFFER_BENEFIT.Action::ANY,
            [Permission::OFFER_BENEFIT.Action::ANY],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'Admin can access same module' => [
            Permission::OFFER_BENEFIT.Action::VIEW,
            [Permission::OFFER_BENEFIT.Action::ANY],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'Admin should not access different module' => [
            Permission::OFFER_CARDS.Action::VIEW,
            [Permission::OFFER_BENEFIT.Action::ANY],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'User with view access only should only access to view module' => [
            Permission::OFFER_BENEFIT.Action::VIEW,
            [Permission::OFFER_BENEFIT.Action::VIEW],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'User who has permission to view module(A) should not get permission to view module(b)' => [
            Permission::OFFER_BENEFIT.Action::VIEW,
            [Permission::OFFER_CARDS.Action::VIEW],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'Admin who permission has (*) should access to entire system' => [
            'something.test',
            [Action::ANY],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'Admin who permission has (*) should access to any other wildcard permission' => [
            'something.*',
            [Action::ANY],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'Admin who has multiple permissions that does not include module permission' => [
            Permission::OFFER_BENEFIT.Action::ANY,
            ['offers.locations.*', 'offers.something.*'],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'Admin who has multiple permissions that does include exact module permission' => [
            Permission::OFFER_BENEFIT.Action::ANY,
            ['offers.locations.*', Permission::OFFER_BENEFIT.Action::ANY],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'Admin who has multiple permission that has one view permission for the module' => [
            Permission::OFFER_BENEFIT.Action::VIEW,
            [Permission::OFFER_BENEFIT.Action::VIEW, Permission::OFFER_CARDS.Action::VIEW],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'User with at least one permission matching wildcard should be granted' => [
            Permission::OFFER_BENEFIT.Action::ANY,
            [Permission::OFFER_CARDS.Action::VIEW, Permission::OFFER_BENEFIT.Action::VIEW],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'User with no permission matching wildcard should be denied' => [
            Permission::OFFER_BENEFIT.Action::ANY,
            [Permission::OFFER_CARDS.Action::VIEW, Permission::OFFER_BRAND.Action::VIEW],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'Wildcard inside the attribute should grant access if user has at least one matching permission' => [
            'offer.*.view',
            [Permission::OFFER_CARDS.Action::VIEW, Permission::OFFER_BRAND.Action::VIEW],
            VoterInterface::ACCESS_GRANTED,
        ];

        yield 'Wildcard inside the attribute should deny access if user does not have any matching permission' => [
            'offers.*.delete',
            [Permission::OFFER_CARDS.Action::VIEW, Permission::OFFER_BRAND.Action::VIEW],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'Dots are properly escaped in the attribute' => [
            'offers.*.view',
            ['offers.locationaview'],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'Dots are properly escaped in permission list' => [
            'offers.*aview',
            [Permission::OFFER_CARDS.Action::VIEW],
            VoterInterface::ACCESS_DENIED,
        ];

        yield 'Does not allow to bypass 2FA with wildcard permission' => [
            'IS_AUTHENTICATED_2FA_IN_PROGRESS',
            [Action::ANY],
            VoterInterface::ACCESS_ABSTAIN,
        ];
    }

    /**
     * @param string[] $permissions
     */
    private function getUser(array $permissions): Administrator
    {
        $user = $this->createStub(Administrator::class);
        $user->method('getPermissions')->willReturn($permissions);

        return $user;
    }
}
