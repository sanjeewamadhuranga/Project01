<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\ActivityLog\ActivityLogType;
use App\Domain\Document\Log\ChangeSet;
use App\Domain\Document\Log\Details;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[When('dev'), When('prod')]
#[AutoconfigureTag('doctrine_mongodb.odm.event_listener', ['event' => 'onFlush']), AutoconfigureTag('doctrine_mongodb.odm.event_listener', ['event' => 'postFlush'])]
class ActivityLogListener
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage, private readonly ChangeSetProvider $changeSetProvider)
    {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getDocumentManager();
        $uow = $em->getUnitOfWork();
        $classMetadata = $em->getClassMetadata(Log::class);

        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof Administrator) {
            return;
        }

        foreach ($uow->getScheduledDocumentInsertions() as $document) {
            if ($document instanceof Log
                || $document instanceof ChangeSet
                || $document instanceof Details
            ) {
                return;
            }

            $log = new Log(ActivityLogType::COMMIT_CREATE, $document);
            $em->persist($log);
            $uow->computeChangeSet($classMetadata, $log);
        }

        foreach ($uow->getScheduledDocumentUpdates() as $document) {
            if ($document instanceof Log
                || $document instanceof ChangeSet
                || $document instanceof Details
            ) {
                return;
            }

            $classMetadata = $em->getClassMetadata(Log::class);
            $log = new Log(ActivityLogType::COMMIT_UPDATE, $document);
            $log->setChangeSets($this->changeSetProvider->getChangeSets($document, $uow));
            $em->persist($log);
            $uow->computeChangeSet($classMetadata, $log);
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $em = $args->getDocumentManager();

        $em->clear(Log::class);
        $em->clear(ChangeSet::class);
        $em->clear(Details::class);
    }
}
