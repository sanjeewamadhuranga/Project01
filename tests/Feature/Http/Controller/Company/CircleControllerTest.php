<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Domain\Document\Circles;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Repository\CirclesRepository;
use App\Tests\Feature\BaseTestCase;

class CircleControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsCirclesList(): void
    {
        self::$client->request('GET', '/circles');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('circles-list');
    }

    /**
     * @group smoke
     */
    public function testItListsCircles(): void
    {
        self::$client->request('GET', '/circles/list');

        $this->assertGridResponse();
    }

    /**
     * @group smoke
     */
    public function testItShowsCircleDetails(): void
    {
        /** @var Circles $testCircle */
        $testCircle = self::$fixtures['circle_test'];
        self::$client->request('GET', sprintf('/circles/%s', $testCircle->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'test');
    }

    public function testItAllowsToDeleteACircle(): void
    {
        $this->markTouchesDb();
        /** @var Circles $testCircle */
        $testCircle = self::$fixtures['circle_test'];
        self::$client->request('DELETE', sprintf('/circles/%s/delete', $testCircle->getId()));

        self::assertResponseIsSuccessful();
        $testCircle = $this->getDocumentManager()->find(Circles::class, $testCircle->getId());
        self::assertInstanceOf(Circles::class, $testCircle);
        self::assertTrue($testCircle->isDeleted());
    }

    public function testItAddMerchantToACircle(): void
    {
        $this->markTouchesDb();
        /** @var Circles $testCircle */
        $testCircle = self::$fixtures['circle_test'];
        /** @var Company $company */
        $company = self::$fixtures['test_company_1'];
        self::$client->jsonRequest(
            'POST',
            sprintf('/circles/%s/add-merchants', $testCircle->getId()),
            ['merchantIds' => [$company->getId()]]
        );

        self::assertResponseIsSuccessful();
        $testCircle = $this->refresh($testCircle);
        self::assertContains($company->getId(), $testCircle->getCompanies()->map(fn (Company $company) => $company->getId()));
    }

    public function testItCreatesCircle(): void
    {
        $this->markTouchesDb();
        /** @var Administrator $admin */
        $admin = self::$fixtures['user_ryan'];
        self::$client->request('GET', '/circles/create');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Create circles');

        self::$client->submitForm('Submit', [
            'circles' => [
                'name' => 'circle test',
                'description' => 'circle description',
                'user' => $admin->getId(),
            ],
        ]);

        $circle = self::getContainer()->get(CirclesRepository::class)->findOneBy(['name' => 'circle test']);
        self::assertInstanceOf(Circles::class, $circle);
        self::assertSame('circle description', $circle->getDescription());
        self::assertSame($admin->getId(), $circle->getUser()[0]?->getId());
    }

    public function testItUpdateCircle(): void
    {
        $this->markTouchesDb();
        /** @var Circles $circle */
        $circle = self::$fixtures['circle_test'];
        /** @var Administrator $admin */
        $admin = self::$fixtures['user_ryan'];

        self::$client->request('GET', sprintf('/circles/%s/edit', $circle->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Update circles');

        self::$client->submitForm('Submit', [
            'circles' => [
                'name' => 'circle update',
                'description' => 'circle description',
                'user' => $admin->getId(),
            ],
        ]);

        $circle = $this->refresh($circle);
        self::assertSame('circle update', $circle->getName());
        self::assertSame($admin->getId(), $circle->getUser()[0]?->getId());
    }
}
