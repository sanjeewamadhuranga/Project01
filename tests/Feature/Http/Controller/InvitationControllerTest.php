<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Invitation;
use App\Domain\Document\Role\Role;
use App\Infrastructure\Repository\InvitationRepository;
use App\Tests\Feature\BaseTestCase;

class InvitationControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsInvitationList(): void
    {
        self::$client->request('GET', '/onboarding/invitation');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('onboarding-invitation-list');
    }

    /**
     * @group smoke
     */
    public function testItListsInvitation(): void
    {
        self::$client->request('GET', '/onboarding/invitation/list');

        $this->assertGridResponse();
    }

    public function testItAllowsToDeleteAInvitation(): void
    {
        /** @var Invitation $testInvitation */
        $testInvitation = self::$fixtures['invitation_test'];
        self::$client->request('DELETE', sprintf('/onboarding/invitation/%s/delete', $testInvitation->getId()));

        self::assertResponseIsSuccessful();
        $testInvitation = $this->getDocumentManager()->find(Invitation::class, $testInvitation->getId());
        self::assertInstanceOf(Invitation::class, $testInvitation);
        self::assertTrue($testInvitation->isDeleted());
    }

    public function testItCreatesInvitation(): void
    {
        /** @var Role $role */
        $role = self::$fixtures['test_role_1'];
        /** @var Company $company */
        $company = self::$fixtures['test_company_1'];

        self::$client->request('GET', '/onboarding/invitation/create');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Create invitation');

        self::$client->submitForm('Submit', [
            'invitation' => [
                'email' => 'test@test.com',
                'invitationCode' => 'test',
                'company' => $company->getId(),
                'roles' => [$role->getId()],
            ],
        ]);

        $invitation = self::getContainer()->get(InvitationRepository::class)->findOneBy(['email' => 'test@test.com']);
        self::assertInstanceOf(Invitation::class, $invitation);
    }
}
