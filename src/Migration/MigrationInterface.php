<?php

declare(strict_types=1);

namespace App\Migration;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.migration')]
interface MigrationInterface
{
    public function down(): void;

    public function up(): void;

    public function getDescription(): ?string;

    public static function getKey(): string;
}
