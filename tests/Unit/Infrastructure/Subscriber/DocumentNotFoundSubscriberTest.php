<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Subscriber;

use App\Infrastructure\Subscriber\DocumentNotFoundSubscriber;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\DocumentNotFoundEventArgs;
use Psr\Log\LoggerInterface;
use stdClass;

class DocumentNotFoundSubscriberTest extends UnitTestCase
{
    public function testItLogsErrorAndDisablesException(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error');

        $subscriber = new DocumentNotFoundSubscriber();
        $subscriber->setLogger($logger);

        $event = new DocumentNotFoundEventArgs(new stdClass(), $this->createStub(DocumentManager::class), 'aaa-bbb-ccc-ddd');
        self::assertFalse($event->isExceptionDisabled());
        $subscriber->documentNotFound($event);
        self::assertTrue($event->isExceptionDisabled());
    }
}
