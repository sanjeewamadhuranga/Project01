<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Location\Location;
use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Transaction\Transaction;
use App\Tests\Extension\MissingTranslationsExtension;
use App\Tests\Feature\Traits\JsonResponseTrait;
use App\Tests\Feature\Traits\MessageBusTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Happyr\ServiceMocking\Test\RestoreServiceContainer;
use MongoDB\Driver\Session;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Base feature test class. Provides useful methods for checking JSON responses and loads fixtures when needed.
 *
 * Note that fixtures will only be reloaded if $loadFixtures is set to true AND markTouchesDb() has been called.
 * This is to speed up the tests but may introduce some unexpected side effects if one forgets to call markTouchesDb().
 *
 * Additionally, $authenticate property can be used to control whether the test client should be authenticated or not.
 * By default, the client is authenticated with default test user, but testing some public endpoints may require to set it to false.
 */
abstract class BaseTestCase extends WebTestCase
{
    use JsonResponseTrait;
    use RestoreServiceContainer;
    use MessageBusTrait;

    protected static KernelBrowser $client;

    protected static bool $loadFixtures = true;

    protected static bool $authenticate = true;

    /**
     * @var array|object[]
     */
    protected static array $fixtures = [];

    protected static Session $session;

    private static bool $touchesDb = false;

    protected static DocumentManager $dm;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$booted) {
            self::$client = self::createClient();
        }

        $this->replaceBus();

        Fixtures::$loader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine_mongodb');
        self::$dm = self::getContainer()->get('doctrine_mongodb.odm.document_manager');

        if (!static::$loadFixtures) {
            return;
        }

        $this->loadFixtures();
        self::$touchesDb = false;

        $this->getDocumentManager()->clear();

        if (static::$authenticate) {
            $this->authenticate();
        }
    }

    protected function markTouchesDb(): void
    {
        self::$touchesDb = true;
    }

    protected function tearDown(): void
    {
        $this->getDocumentManager()->clear();
        $translator = self::getContainer()->get(TranslatorInterface::class);
        MissingTranslationsExtension::addMessages(static::class.'::'.$this->getName(), $translator->getCollectedMessages());

        parent::tearDown();
    }

    /**
     * @return object[]
     */
    protected function loadFixtures(): array
    {
        return self::$fixtures = Fixtures::loadFixtures(self::$touchesDb);
    }

    protected function getTestCompany(): Company
    {
        return self::$fixtures['test_company_1']; // @phpstan-ignore-line
    }

    protected function getTestLocation(): Location
    {
        return self::$fixtures['location_test_1']; // @phpstan-ignore-line
    }

    protected function getTestTransaction(): Transaction
    {
        return self::$fixtures['transaction_confirmed_test_company']; // @phpstan-ignore-line
    }

    protected function getTestUser(): Administrator
    {
        return self::$fixtures['user_test']; // @phpstan-ignore-line
    }

    protected function getDocumentManager(): DocumentManager
    {
        return self::$dm;
    }

    protected function authenticate(?Administrator $user = null): KernelBrowser
    {
        return self::$client->loginUser($user ?? $this->getTestUser());
    }

    /**
     * @template T of BaseDocument
     *
     * @param T $document
     *
     * @return T
     */
    protected function refresh(BaseDocument $document): BaseDocument
    {
        /** @var T|null $newDocument */
        $newDocument = $this->getDocumentManager()->find($document::class, $document->getId());

        if (null === $newDocument) {
            throw DocumentNotFoundException::documentNotFound($document::class, $document->getId());
        }

        $this->getDocumentManager()->refresh($newDocument);

        return $newDocument;
    }
}
