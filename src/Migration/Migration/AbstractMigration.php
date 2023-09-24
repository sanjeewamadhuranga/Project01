<?php

declare(strict_types=1);

namespace App\Migration\Migration;

use App\Migration\MigrationInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

abstract class AbstractMigration implements MigrationInterface
{
    public function __construct(protected DocumentManager $dm)
    {
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public static function getKey(): string
    {
        return preg_replace('/[\D]/', '', static::class) ?? '';
    }
}
