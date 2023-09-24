<?php

declare(strict_types=1);

namespace App\Tests\Feature\Traits;

use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Hydrator\HydratorFactory;
use Doctrine\ODM\MongoDB\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;

trait DocumentManagerAwareTrait
{
    private DocumentManager&MockObject $documentManager;

    private function initializeDocumentManager(): void
    {
        $eventManager = $this->createStub(EventManager::class);

        $this->documentManager = $this->createMock(DocumentManager::class);
        $this->documentManager->method('getUnitOfWork')->willReturn(new UnitOfWork(
            $this->documentManager,
            $eventManager,
            new HydratorFactory(
                $this->documentManager,
                $eventManager,
                '/tmp',
                '/tmp',
                0
            )
        ));
    }
}
