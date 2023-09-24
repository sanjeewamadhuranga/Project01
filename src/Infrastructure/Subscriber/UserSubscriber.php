<?php

declare(strict_types=1);

namespace App\Infrastructure\Subscriber;

use App\Application\Security\UserUpdater;
use App\Domain\Document\Security\Administrator;
use Doctrine\Bundle\MongoDBBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly UserUpdater $userUpdater)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $event
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $user = $event->getObject();

        if ($user instanceof Administrator) {
            $this->userUpdater->updateUser($user);
        }
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $event
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $user = $event->getObject();

        if ($user instanceof Administrator) {
            $this->userUpdater->updateUser($user);
            $this->recomputeChangeSet($event, $user);
        }
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $event
     */
    private function recomputeChangeSet(LifecycleEventArgs $event, Administrator $user): void
    {
        $om = $event->getObjectManager();
        if ($om instanceof DocumentManager) {
            $om->getUnitOfWork()->recomputeSingleDocumentChangeSet($om->getClassMetadata($user::class), $user);
        }
    }
}
