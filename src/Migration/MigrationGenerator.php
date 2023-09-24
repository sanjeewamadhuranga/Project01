<?php

declare(strict_types=1);

namespace App\Migration;

use DateTime;

class MigrationGenerator
{
    private static string $fileTemplate = <<<TEMPLATE
        <?php

        declare(strict_types=1);

        namespace App\Migration\Migration;

        final class Migration<timestamp> extends AbstractMigration
        {
            public function up(): void
            {
                // TODO: Add method body
            }

            public function down(): void
            {
                // TODO: Add method body
            }
        }

        TEMPLATE;

    public function generateMigrationFile(): string
    {
        $timestamp = (new DateTime())->format('YmdHis');
        $fileName = 'Migration'.$timestamp;
        $filePath = sprintf('%s/Migration/%s.php', __DIR__, $fileName);

        file_put_contents($filePath, str_replace('<timestamp>', $timestamp, self::$fileTemplate));

        return $fileName;
    }
}
