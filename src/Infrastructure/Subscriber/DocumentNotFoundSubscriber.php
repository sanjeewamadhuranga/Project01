<?php

declare(strict_types=1);

namespace App\Infrastructure\Subscriber;

use Doctrine\Bundle\MongoDBBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\Event\DocumentNotFoundEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class DocumentNotFoundSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getSubscribedEvents(): array
    {
        return [Events::documentNotFound];
    }

    public function documentNotFound(DocumentNotFoundEventArgs $event): void
    {
        $this->logger?->error(DocumentNotFoundException::documentNotFound(
            $event->getDocument()::class,
            $event->getIdentifier()
        )->getMessage());

        $event->disableException();
    }
}
