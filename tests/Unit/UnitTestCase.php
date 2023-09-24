<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Hydrator\HydratorFactory;
use Doctrine\ODM\MongoDB\UnitOfWork;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    protected function getUnitOfWork(DocumentManager $documentManager): UnitOfWork
    {
        $eventManager = $this->createStub(EventManager::class);
        $hydratorFactory = new HydratorFactory(
            $documentManager,
            $eventManager,
            '/tmp',
            'Hydrator',
            Configuration::AUTOGENERATE_NEVER
        );

        return new UnitOfWork($documentManager, $eventManager, $hydratorFactory);
    }
}
