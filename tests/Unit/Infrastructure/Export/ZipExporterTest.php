<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Export;

use App\Infrastructure\Export\ZipExporter;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class ZipExporterTest extends UnitTestCase
{
    private ZipExporter $exporter;

    protected function setUp(): void
    {
        $this->exporter = new ZipExporter();

        parent::setUp();
    }

    public function testItReturnsStreamedResponseWithHeaders(): void
    {
        $fileName = 'test.zip';

        $response = $this->exporter->export(
            $fileName,
            [1, 2, 3],
            fn (ZipStream $zip, int $number) => $zip->addFile(uniqid('file', true).'.txt', (string) $number)
        );

        self::assertInstanceOf(StreamedResponse::class, $response);

        $headers = $response->headers->all();
        unset($headers['date']);

        self::assertSame([
            'content-disposition' => [HeaderUtils::makeDisposition('attachment', $fileName)],
            'content-type' => ['application/zip'],
            'pragma' => ['public'],
            'cache-control' => ['must-revalidate, public'],
            'content-transfer-encoding' => ['binary'],
        ], $headers);
    }
}
