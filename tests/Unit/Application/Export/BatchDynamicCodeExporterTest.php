<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Export;

use App\Application\Export\BatchDynamicCodeExporter;
use App\Domain\Document\BatchDynamicCode;
use App\Domain\Document\DynamicCode;
use App\Infrastructure\Export\ZipExporter;
use App\Infrastructure\Qr\QrBuilder;
use App\Infrastructure\Repository\DynamicCodeRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;

class BatchDynamicCodeExporterTest extends ZipExportTest
{
    private DynamicCodeRepository&MockObject $repository;

    private QrBuilder&Stub $qrBuilder;

    private ZipExporter $zipExporter;

    private BatchDynamicCodeExporter $subject;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(DynamicCodeRepository::class);
        $this->qrBuilder = $this->createStub(QrBuilder::class);
        $this->zipExporter = new ZipExporter();
        $this->qrBuilder = $this->createStub(QrBuilder::class);

        $this->subject = new BatchDynamicCodeExporter($this->repository, $this->qrBuilder, $this->zipExporter);

        parent::setUp();
    }

    public function testItExportsBatchDynamicCodesAsZipContainingImageFiles(): void
    {
        $code1 = new DynamicCode($this->createStub(BatchDynamicCode::class));
        $code1->setCode('test-code');

        $code2 = new DynamicCode($this->createStub(BatchDynamicCode::class));
        $code2->setCode('test-code2');

        $batchCode = new BatchDynamicCode();
        $batchCode->setTitle('Test Batch');

        $this->repository->expects(self::once())
            ->method('getDynamicCodes')
            ->with($batchCode)
            ->willReturn([$code1, $code2]);

        $this->qrBuilder->method('build')->willReturnCallback(fn (DynamicCode $code) => sprintf('%s.qr-content', $code->getCode()));

        $response = $this->subject->exportCodes($batchCode);

        ob_start();
        $response->send();
        $files = $this->getZipContents((string) ob_get_clean());

        self::assertSame('attachment; filename=Test_Batch.zip', $response->headers->get('Content-Disposition'));
        self::assertSame('application/zip', $response->headers->get('Content-Type'));
        self::assertCount(2, $files);
        self::assertSame('test-code.qr-content', $files['test-code.png']);
        self::assertSame('test-code2.qr-content', $files['test-code2.png']);
    }
}
