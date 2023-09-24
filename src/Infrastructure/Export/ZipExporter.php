<?php

declare(strict_types=1);

namespace App\Infrastructure\Export;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class ZipExporter
{
    /**
     * @template T of mixed
     *
     * @param iterable<T>                  $items
     * @param callable(ZipStream, T): void $callback
     */
    public function export(string $name, iterable $items, callable $callback): Response
    {
        $options = new Archive();
        $options->setZeroHeader(true);
        $options->setEnableZip64(false);

        $disposition = HeaderUtils::makeDisposition('attachment', $name);
        $zip = new ZipStream($name, $options);

        return new StreamedResponse(
            function () use ($items, $callback, $zip): void {
                // set time limit to 5min
                set_time_limit(3000);

                foreach ($items as $item) {
                    $callback($zip, $item);
                }

                $zip->finish();
            },
            Response::HTTP_OK,
            [
                'Content-Disposition' => $disposition,
                'Content-Type' => 'application/zip',
                'Pragma' => 'public',
                'Cache-Control' => 'public, must-revalidate',
                'Content-Transfer-Encoding' => 'binary',
            ]
        );
    }
}
