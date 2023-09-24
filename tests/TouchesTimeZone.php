<?php

declare(strict_types=1);

namespace App\Tests;

trait TouchesTimeZone
{
    private string $originalTimezone;

    /**
     * @before
     */
    public function changeTimeZone(): void
    {
        $this->originalTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    /**
     * @after
     */
    public function restoreTimeZone(): void
    {
        date_default_timezone_set($this->originalTimezone);
    }
}
