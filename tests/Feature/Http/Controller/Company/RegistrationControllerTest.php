<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Domain\Document\Company\Registration;
use App\Tests\Feature\BaseTestCase;

class RegistrationControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsRegistrationList(): void
    {
        self::$client->request('GET', '/onboarding/registration');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('onboarding-registration-list');
    }

    /**
     * @group smoke
     */
    public function testItListsRegistration(): void
    {
        self::$client->request('GET', '/onboarding/registration/list');

        $this->assertGridResponse();
    }

    public function testItAllowsToDeleteARegistration(): void
    {
        /** @var Registration $registration */
        $registration = self::$fixtures['registration_test'];
        self::$client->request('DELETE', sprintf('/onboarding/registration/%s/delete', $registration->getId()));

        self::assertResponseIsSuccessful();
        $registration = $this->getDocumentManager()->find(Registration::class, $registration->getId());
        self::assertInstanceOf(Registration::class, $registration);
        self::assertTrue($registration->isDeleted());
    }
}
