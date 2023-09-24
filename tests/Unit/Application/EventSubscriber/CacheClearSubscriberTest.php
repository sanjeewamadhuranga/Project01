<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\EventSubscriber;

use App\Application\EventSubscriber\CacheClearSubscriber;
use App\Application\Queue\Bus;
use App\Application\Queue\Commands\ClearCache;
use App\Domain\Document\Addons\BetaFeature;
use App\Domain\Document\ApiStatus;
use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Country;
use App\Domain\Document\Provider\Provider;
use App\Domain\Document\Role\Role;
use App\Domain\Document\Setting;
use App\Domain\Document\Transaction\Transaction;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sentry\State\HubInterface;
use Throwable;

class CacheClearSubscriberTest extends UnitTestCase
{
    private Bus&MockObject $bus;

    private HubInterface&MockObject $sentry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = $this->createMock(Bus::class);
        $this->sentry = $this->createMock(HubInterface::class);
    }

    /**
     * @testWith ["dev"]
     *           ["test"]
     */
    public function testItNoopsOnEnvironment(string $environment): void
    {
        $subscriber = new CacheClearSubscriber($this->bus, $environment, $this->sentry);
        $this->bus->expects(self::never())->method('dispatch');

        $subscriber->postUpdate($this->createStub(LifecycleEventArgs::class));
        $subscriber->postPersist($this->createStub(LifecycleEventArgs::class));
    }

    public function testItNoopsIfDocumentShouldNotTriggerCacheClear(): void
    {
        $subscriber = new CacheClearSubscriber($this->bus, 'prod', $this->sentry);
        $this->bus->expects(self::never())->method('dispatch');

        $subscriber->postUpdate($this->getEventArgs(Transaction::class));
        $subscriber->postPersist($this->getEventArgs(Company::class));
    }

    /**
     * @param class-string<BaseDocument> $documentClass
     *
     * @dataProvider supportedClassesProvider
     */
    public function testItClearsCacheForSupportedClass(string $documentClass): void
    {
        $subscriber = new CacheClearSubscriber($this->bus, 'prod', $this->sentry);
        $subscriber->setLogger(new NullLogger());
        $this->bus->expects(self::exactly(2))->method('dispatch')->with(new ClearCache());

        $subscriber->postUpdate($this->getEventArgs($documentClass));
        $subscriber->postPersist($this->getEventArgs($documentClass));
    }

    public function testItLogsAndReportsQueueError(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $subscriber = new CacheClearSubscriber($this->bus, 'prod', $this->sentry);
        $subscriber->setLogger($logger);
        $exception = $this->createStub(Throwable::class);

        $this->bus->expects(self::once())->method('dispatch')->with(new ClearCache())->willThrowException($exception);
        $this->sentry->expects(self::once())->method('captureException')->with($exception);
        $logger->expects(self::once())->method('error');

        $subscriber->postUpdate($this->getEventArgs(Setting::class));
    }

    public function testItIsSubscribedToPostPersistAndPostUpdate(): void
    {
        self::assertSame([Events::postPersist, Events::postUpdate], (new CacheClearSubscriber($this->bus, 'prod', $this->sentry))->getSubscribedEvents());
    }

    /**
     * @return iterable<string, array{class-string<BaseDocument>}>
     */
    public function supportedClassesProvider(): iterable
    {
        yield 'Setting' => [Setting::class];
        yield 'Provider' => [Provider::class];
        yield 'BetaFeature' => [BetaFeature::class];
        yield 'Country' => [Country::class];
        yield 'Role' => [Role::class];
        yield 'ApiStatus' => [ApiStatus::class];
    }

    /**
     * @param class-string<BaseDocument> $documentClass
     */
    private function getEventArgs(string $documentClass): LifecycleEventArgs
    {
        return new LifecycleEventArgs(
            $this->createStub($documentClass),
            $this->createStub(DocumentManager::class)
        );
    }
}
