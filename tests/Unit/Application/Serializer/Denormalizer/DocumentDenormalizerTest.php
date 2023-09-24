<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Serializer\Denormalizer;

use App\Application\Company\Intercom;
use App\Application\Serializer\Denormalizer\DocumentDenormalizer;
use App\Domain\Document\Circles;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User as CompanyUser;
use App\Domain\Document\Security\Administrator;
use App\Domain\Document\Transaction\Transaction;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\User\UserInterface;

class DocumentDenormalizerTest extends UnitTestCase
{
    private readonly DocumentManager&MockObject $documentManager;

    private readonly DocumentDenormalizer $denormalizer;

    protected function setUp(): void
    {
        $this->documentManager = $this->createMock(DocumentManager::class);

        $this->denormalizer = new DocumentDenormalizer($this->documentManager);
    }

    public function testItThrowsExceptionWhenDocumentIsNotFound(): void
    {
        $this->documentManager->method('find')->willReturn(null);

        $type = Company::class;
        $data = '6218f43f1c4e37088d32f700';

        $this->expectException(DocumentNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Document %s with id %s not found.', $type, $data));

        $this->denormalizer->denormalize($data, $type);
    }

    public function testItReturnsDocumentIfFound(): void
    {
        $type = Transaction::class;
        $document = $this->createStub($type);

        $this->documentManager->method('find')->willReturn($document);

        self::assertSame($document, $this->denormalizer->denormalize('6218f43f1c4e37088d32f701', $type));
    }

    /**
     * @dataProvider baseDocumentProvider
     */
    public function testItSupportsBaseDocuments(string $class): void
    {
        self::assertTrue($this->denormalizer->supportsDenormalization('someString', $class));
    }

    /**
     * @dataProvider wrongTypeOrDataProvider
     */
    public function testItDoesNotSupportsWrongTypeOrData(mixed $data, string $type): void
    {
        self::assertFalse($this->denormalizer->supportsDenormalization($data, $type));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public function baseDocumentProvider(): iterable
    {
        yield 'Company' => [Company::class];
        yield 'Transaction' => [Transaction::class];
        yield 'User' => [Administrator::class];
        yield 'CompanyUser' => [CompanyUser::class];
        yield 'Circles' => [Circles::class];
    }

    /**
     * @return iterable<string, array{mixed, string}>
     */
    public function wrongTypeOrDataProvider(): iterable
    {
        yield 'Array with ID' => [['id' => '6218f191d7b6d3517816a5a2'], Company::class];
        yield 'Incorrect class' => ['6218f43f1c4e37088d32f6fd', Intercom::class];
        yield 'Incorrect user' => ['6218f43f1c4e37088d32f6fe', UserInterface::class];
    }
}
