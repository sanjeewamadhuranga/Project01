<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\EventSubscriber;

use App\Application\EventSubscriber\CacheClearSubscriber;
use App\Tests\Feature\BaseTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Events;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class CacheClearSubscriberTest extends BaseTestCase
{
    protected static bool $loadFixtures = false;

    public function testItIsRegisteredAsDoctrineEventSubscriber(): void
    {
        $subscriber = self::getContainer()->get(CacheClearSubscriber::class);
        $eventManager = self::getContainer()->get(DocumentManager::class)->getEventManager();

        self::assertContains($subscriber, $eventManager->getListeners(Events::postPersist));
        self::assertContains($subscriber, $eventManager->getListeners(Events::postUpdate));
    }

    public function testItInjectsLogger(): void
    {
        $subscriber = self::getContainer()->get(CacheClearSubscriber::class);
        $reflectionClass = new ReflectionClass($subscriber);

        $logger = $reflectionClass->getProperty('logger');
        $logger->setAccessible(true);

        self::assertInstanceOf(LoggerInterface::class, $logger->getValue($subscriber));
    }
}
