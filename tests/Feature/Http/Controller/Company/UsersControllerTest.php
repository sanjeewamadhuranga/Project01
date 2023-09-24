<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Domain\Document\Company\Address;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Tests\Feature\BaseTestCase;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\HttpFoundation\Response;

class UsersControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsUsersTab(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users', $this->getTestCompany()->getId()));

        self::assertSelectorExists('company-users-list');
    }

    /**
     * @group smoke
     */
    public function testItListsUsers(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/list', $this->getTestCompany()->getId()));

        $this->assertGridResponse();
        self::assertCount(1, $this->getJsonResponse()['data']);
    }

    public function testItShowsUserDetails(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/test-sub-id', $this->getTestCompany()->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Simon Test');
    }

    public function testItThrows404WhenUserIsNotFound(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/invalid-id', $this->getTestCompany()->getId()));

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testItEditsUser(): void
    {
        $this->markTouchesDb();
        $testCompany = $this->getTestCompany();
        $this->openUserEditForm($testCompany, 'test-sub-id');
        self::assertSelectorTextContains('html', 'Simon Test');

        self::$client->submitForm('Submit', [
            'user' => [
                'mobile' => '+123456789',
                'state' => 'suspended',
                'dob' => '2020-10-05',
                'addresses' => [
                    'street' => 'Cecil Street',
                ],
            ],
        ]);
        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        $user = $testCompany->getUser('test-sub-id');
        self::assertInstanceOf(User::class, $user);
        self::assertInstanceOf(Address::class, $user->getAddresses());
        self::assertSame('2020-10-05', $user->getDob());
        self::assertSame('+123456789', $user->getMobile());
        self::assertSame('Cecil Street', $user->getAddresses()->getStreet());
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testItShowsErrorWhenInvalidDataProvided(): void
    {
        $testCompany = $this->getTestCompany();
        $this->openUserEditForm($testCompany, 'test-sub-id');
        self::$client->submitForm('Submit', [
            'user' => [
                'mobile' => '+1',
                'state' => 'suspended',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorExists('#user_mobile.is-invalid');
        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        $user = $testCompany->getUser('test-sub-id');
        self::assertInstanceOf(User::class, $user);
        self::assertNull($user->getDob());
        self::assertNull($user->getMobile());
    }

    public function testItRequiresAddressWhenKycIsChecked(): void
    {
        $testCompany = $this->getTestCompany();
        $this->openUserEditForm($testCompany, 'test-sub-id');
        self::$client->submitForm('Submit', [
            'user' => [
                'requireKyc' => '1',
                'dob' => '2020',
                'state' => 'suspended',
                'addresses' => [
                    'street' => '',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorExists('#user_addresses_street.is-invalid');
        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        $user = $testCompany->getUser('test-sub-id');
        self::assertInstanceOf(User::class, $user);
        self::assertNull($user->getDob());
    }

    public function testItDoesNotRequireAddressWhenKycIsNotChecked(): void
    {
        $this->markTouchesDb();
        $testCompany = $this->getTestCompany();
        $this->openUserEditForm($testCompany, 'test-sub-id');
        $form = self::$client->getCrawler()->selectButton('Submit')->form();
        /** @var ChoiceFormField $kyc */
        $kyc = $form['user[requireKyc]'];
        $kyc->untick();

        $form->setValues([
            'user' => [
                'state' => 'suspended',
                'addresses' => ['street' => ''],
            ],
        ]);

        self::assertResponseIsSuccessful();
        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        $user = $testCompany->getUser('test-sub-id');
        self::assertInstanceOf(User::class, $user);
        self::assertNull($user->getAddresses());
    }

    private function openUserEditForm(Company $company, string $sub): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/users/%s/edit', $company->getId(), $sub));
        self::assertResponseIsSuccessful();
    }
}
