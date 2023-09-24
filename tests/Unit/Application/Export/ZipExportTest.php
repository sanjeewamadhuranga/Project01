<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Export;

use App\Tests\Unit\UnitTestCase;
use ZipArchive;

abstract class ZipExportTest extends UnitTestCase
{
    private string $tempFileName;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempFileName = (string) tempnam(sys_get_temp_dir(), 'zipstreamtest');
    }

    protected function tearDown(): void
    {
        @unlink($this->tempFileName);
        set_time_limit(0);

        parent::tearDown();
    }

    /**
     * @return array<string, string>
     */
    protected function getZipContents(string $zipContent): array
    {
        file_put_contents($this->tempFileName, $zipContent);
        $zip = new ZipArchive();
        $zip->open($this->tempFileName);

        $files = [];
        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $files[(string) $zip->getNameIndex($i)] = (string) $zip->getFromIndex($i);
        }

        return $files;
    }
}
