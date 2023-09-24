<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener;

use App\Application\Listener\ChangeSetProvider;
use App\Domain\Document\Log\ChangeSet;
use App\Domain\Document\Security\Administrator;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;

class ChangeSetProviderTest extends UnitTestCase
{
    public function testItReplacesSensitiveInformationWithAPlaceHolderOnlyWhenClassPropertyHasMaskAttribute(): void
    {
        $administrator = $this->createStub(Administrator::class);
        $documentManager = $this->createMock(DocumentManager::class);
        $uow = $this->getUnitOfWork($documentManager);
        $uow->setDocumentChangeSet($administrator, [
            'googleAuthenticatorSecret' => ['hello', 'testing'],
            'password' => ['asdfasdf', 'asdfasdf'],
            'confirmationToken' => [null, 'secretToken'],
            'locale' => ['en', 'vi'],
        ]);

        $changeSetProvider = new ChangeSetProvider();

        self::assertEqualsCanonicalizing([
            new ChangeSet('googleAuthenticatorSecret', ['*hidden*', '*hidden*']),
            new ChangeSet('password', ['*hidden*', '*hidden*']),
            new ChangeSet('confirmationToken', ['*hidden*', '*hidden*']),
            new ChangeSet('locale', ['en', 'vi']),
        ], $changeSetProvider->getChangeSets($administrator, $uow)->toArray());
    }
}
