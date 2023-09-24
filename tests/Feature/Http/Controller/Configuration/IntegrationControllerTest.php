<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Configuration;

use App\Domain\Document\Integration;
use App\Infrastructure\Repository\IntegrationRepository;
use App\Tests\Feature\BaseTestCase;

class IntegrationControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsIntegrationList(): void
    {
        self::$client->request('GET', '/configuration/integrations');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('configuration-integration-list');
    }

    /**
     * @group smoke
     */
    public function testItListsIntegration(): void
    {
        self::$client->request('GET', '/configuration/integrations/list');

        $this->assertGridResponse();
    }

    /**
     * @group smoke
     */
    public function testItShowsIntegrationDetails(): void
    {
        /** @var Integration $testIntegration */
        $testIntegration = self::$fixtures['integration_test'];
        self::$client->request('GET', sprintf('/configuration/integrations/%s', $testIntegration->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'test');
    }

    public function testItAllowsToDeleteAIntegration(): void
    {
        /** @var Integration $testIntegration */
        $testIntegration = self::$fixtures['integration_test'];
        self::$client->request('DELETE', sprintf('/configuration/integrations/%s/delete', $testIntegration->getId()));

        self::assertResponseIsSuccessful();
        $testIntegration = $this->getDocumentManager()->find(Integration::class, $testIntegration->getId());
        self::assertInstanceOf(Integration::class, $testIntegration);
        self::assertTrue($testIntegration->isDeleted());
    }

    public function testItCreatesIntegration(): void
    {
        self::$client->request('GET', '/configuration/integrations/create');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Create Integration');

        self::$client->submitForm('Submit', [
            'integration' => [
                'name' => 'test',
                'type' => 'CLICKUP',
            ],
        ]);

        $integration = self::getContainer()->get(IntegrationRepository::class)->findOneBy(['name' => 'test']);
        self::assertInstanceOf(Integration::class, $integration);
    }

    public function testItUpdateIntegration(): void
    {
        /** @var Integration $testIntegration */
        $testIntegration = self::$fixtures['integration_test'];

        self::$client->request('GET', sprintf('/configuration/integrations/%s/edit', $testIntegration->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Update Integration');

        self::$client->submitForm('Submit', [
            'integration' => [
                'name' => 'update',
            ],
        ]);

        $this->getDocumentManager()->persist($testIntegration);
        $this->getDocumentManager()->refresh($testIntegration);
        self::assertSame('update', $testIntegration->getName());
    }
}
