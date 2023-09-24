<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener;

use App\Application\Listener\ActivityLogListener;
use App\Application\Listener\ChangeSetProvider;
use App\Domain\ActivityLog\ActivityLogType;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Log\ChangeSet;
use App\Domain\Document\Log\Details;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\UnitOfWork;
use ReflectionClass;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ActivityLogListenerTest extends UnitTestCase
{
    public function testItCallsClearOnPostFlush(): void
    {
        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects(self::exactly(3))->method('clear')->withConsecutive(
            [Log::class],
            [ChangeSet::class],
            [Details::class]
        );

        $listener = new ActivityLogListener($this->createStub(TokenStorage::class), new ChangeSetProvider());
        $event = new PostFlushEventArgs($documentManager);

        $listener->postFlush($event);
    }

    public function testItPersistLogWhenNewDocumentIsCreated(): void
    {
        $newDocument = new Company();
        $documentManager = $this->createMock(DocumentManager::class);

        $uow = $this->getUnitOfWork($documentManager);
        $uow->persist($newDocument);

        $documentManager->method('getUnitOfWork')->willReturn($uow);
        $documentManager->expects(self::once())
            ->method('persist')
            ->with(
                self::callback(fn ($log): bool => $log instanceof Log && ActivityLogType::COMMIT_CREATE === $log->getType() && $newDocument === $log->getObject())
            );

        $this->onFlush($documentManager);
    }

    /**
     * @dataProvider notLoggedDocumentsProvider
     */
    public function testItDoNotPersistLogWhenSomeKindOfDocumentsAreCreated(mixed $newDocument): void
    {
        $documentManager = $this->createMock(DocumentManager::class);
        $uow = $this->getUnitOfWork($documentManager);
        $uow->persist($newDocument);

        $documentManager->method('getUnitOfWork')->willReturn($uow);
        $documentManager->expects(self::never())->method('persist');

        $this->onFlush($documentManager);
    }

    public function testItPersistLogWhenDocumentIsUpdated(): void
    {
        $document = new Company();
        $documentManager = $this->createMock(DocumentManager::class);

        $uow = $this->getUnitOfWork($documentManager);

        $reflectionClass = new ReflectionClass(UnitOfWork::class);
        $reflectionProperty = $reflectionClass->getProperty('documentIdentifiers');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($uow, [spl_object_hash($document) => $document]);
        $uow->scheduleForUpdate($document);

        $documentManager->method('getUnitOfWork')->willReturn($uow);
        $documentManager->expects(self::once())
            ->method('persist')
            ->with(
                self::callback(fn ($log): bool => $log instanceof Log && ActivityLogType::COMMIT_UPDATE === $log->getType() && $document === $log->getObject())
            );

        $this->onFlush($documentManager);
    }

    /**
     * @dataProvider notLoggedDocumentsProvider
     */
    public function testItDoNotPersistLogWhenSomeKindOfDocumentsAreUpdated(mixed $document): void
    {
        $documentManager = $this->createMock(DocumentManager::class);

        $uow = $this->getUnitOfWork($documentManager);

        $reflectionClass = new ReflectionClass(UnitOfWork::class);
        $reflectionProperty = $reflectionClass->getProperty('documentIdentifiers');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($uow, [spl_object_hash($document) => $document]);
        $uow->scheduleForUpdate($document);

        $documentManager->method('getUnitOfWork')->willReturn($uow);
        $documentManager->expects(self::never())->method('persist');

        $this->onFlush($documentManager);
    }

    /**
     * @return iterable<string, array<int, mixed>>
     */
    public function notLoggedDocumentsProvider(): iterable
    {
        yield 'Log object' => [new Log(ActivityLogType::COMMIT_CREATE)];
        yield 'ChangeSet object' => [new ChangeSet('field')];
        yield 'Details object' => [new Details()];
    }

    private function getTokenStorage(): TokenStorageInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($this->createStub(Administrator::class));

        $tokenStorage = $this->createStub(TokenStorage::class);
        $tokenStorage->method('getToken')->willReturn($token);

        return $tokenStorage;
    }

    private function onFlush(DocumentManager $documentManager): void
    {
        $event = new OnFlushEventArgs($documentManager);

        $listener = new ActivityLogListener($this->getTokenStorage(), new ChangeSetProvider());
        $listener->onFlush($event);
    }
}
