<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use Doctrine\Persistence\ObjectRepository;

/**
 * @template TDocumentClass of object
 *
 * @template-extends ObjectRepository<TDocumentClass>
 */
interface Repository extends ObjectRepository
{
    /**
     * @param TDocumentClass $document
     */
    public function save(object $document, bool $flush = true): void;

    /**
     * @return class-string<TDocumentClass>
     */
    public static function objectClass(): string;
}
