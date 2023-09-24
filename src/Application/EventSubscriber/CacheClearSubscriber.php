<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber;

use App\Application\Queue\Bus;
use App\Application\Queue\Commands\ClearCache;
use App\Domain\Document\Addons\BetaFeature;
use App\Domain\Document\ApiStatus;
use App\Domain\Document\Country;
use App\Domain\Document\Provider\Provider;
use App\Domain\Document\Role\Role;
use App\Domain\Document\Setting;
use Doctrine\Bundle\MongoDBBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Sentry\State\HubInterface;
use Throwable;

class CacheClearSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private readonly Bus $bus, private readonly string $environment, private readonly HubInterface $hub)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $this->clearCache($eventArgs);
    }

    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->clearCache($eventArgs);
    }

    private function clearCache(LifecycleEventArgs $eventArgs): void
    {
        if (in_array($this->environment, ['dev', 'test'], true)) {
            return;
        }

        $document = $eventArgs->getDocument();

        if (
            $document instanceof Setting ||
            $document instanceof Provider ||
            $document instanceof BetaFeature ||
            $document instanceof Country ||
            $document instanceof Role ||
            $document instanceof ApiStatus
        ) {
            try {
                $this->bus->dispatch(new ClearCache());
                $this->logger?->info('Published cache clear SNS message');
            } catch (Throwable $e) {
                $this->logger?->error('Error publishing cache clear SNS message');
                $this->hub->captureException($e);
            }
        }
    }
}
