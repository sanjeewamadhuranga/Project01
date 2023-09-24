<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\Upload;

use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Vich\UploaderBundle\EventListener\Doctrine\BaseListener;

/**
 * Fixes {@link https://github.com/dustin10/VichUploaderBundle/issues/1263} issue.
 */
class CleanListener extends BaseListener
{
    public function getSubscribedEvents(): array
    {
        return [
            'preUpdate',
        ];
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $object = $this->adapter->getObjectFromArgs($event);

        if (!$this->isUploadable($object)) {
            return;
        }

        $changeSet = $event->getDocumentChangeSet();

        foreach ($this->getUploadableFields($object) as $field => $fileName) {
            if (!isset($changeSet[$fileName])) {
                continue;
            }

            $this->handler->clean($object, $field);
        }

        $this->adapter->recomputeChangeSet($event);
    }
}
