<?php

declare(strict_types=1);

namespace App\Application\Export;

use App\Domain\Document\BatchDynamicCode;
use App\Domain\Document\DynamicCode;
use App\Infrastructure\Export\ZipExporter;
use App\Infrastructure\Qr\QrBuilder;
use App\Infrastructure\Repository\DynamicCodeRepository;
use Symfony\Component\HttpFoundation\Response;
use ZipStream\ZipStream;

class BatchDynamicCodeExporter
{
    public function __construct(
        private readonly DynamicCodeRepository $repository,
        private readonly QrBuilder $qrBuilder,
        private readonly ZipExporter $zipExporter
    ) {
    }

    public function exportCodes(BatchDynamicCode $batchDynamicCode): Response
    {
        return $this->zipExporter->export(
            $batchDynamicCode->getFilenameForExport(),
            $this->repository->getDynamicCodes($batchDynamicCode),
            fn (ZipStream $zip, DynamicCode $code) => $zip->addFile($code->getFilenameForExport(), $this->qrBuilder->build($code))
        );
    }
}
