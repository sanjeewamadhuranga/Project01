<?php

declare(strict_types=1);

namespace App\Tests\Extension;

use PHPUnit\Runner\AfterLastTestHook;
use Symfony\Component\Translation\DataCollectorTranslator;

class MissingTranslationsExtension implements AfterLastTestHook
{
    /**
     * @var string[]
     */
    private static array $missingTranslations = [];

    /**
     * @param array<array{id: string, state: int}> $messages
     */
    public static function addMessages(string $testCase, array $messages): void
    {
        foreach ($messages as $message) {
            if (DataCollectorTranslator::MESSAGE_MISSING === $message['state']) {
                self::$missingTranslations[] = sprintf('%s - %s', $testCase, $message['id']);
            }
        }
    }

    public static function reset(): void
    {
        self::$missingTranslations = [];
    }

    public function executeAfterLastTest(): void
    {
        if (count(self::$missingTranslations) > 0) {
            echo "\n\nMissing translations: \n\n";

            foreach (self::$missingTranslations as $missingTranslation) {
                echo " - {$missingTranslation}\n";
            }
        }
    }
}
