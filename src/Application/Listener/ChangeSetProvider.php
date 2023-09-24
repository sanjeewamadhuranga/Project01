<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Application\Security\MaskField;
use App\Domain\Document\Log\ChangeSet;
use App\Domain\Document\Security\Administrator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\UnitOfWork;
use ReflectionClass;
use Throwable;

/**
 * This custom ChangeSetProvider provides the hidden placeholder {@see MaskField} when you provide custom
 * MaskField attribute to the document's property {@see Administrator::$googleAuthenticatorSecret}.
 */
class ChangeSetProvider
{
    /**
     * @return Collection<int, ChangeSet>
     */
    public function getChangeSets(object $document, UnitOfWork $unitOfWork): Collection
    {
        $changeSets = new ArrayCollection();
        $reflection = new ReflectionClass($document);

        foreach ($unitOfWork->getDocumentChangeSet($document) as $key => $value) {
            $changeSet = $this->isMasked($reflection, $key)
                ? new ChangeSet($key, [MaskField::PLACEHOLDER, MaskField::PLACEHOLDER])
                : new ChangeSet($key, $value);
            $changeSets->add($changeSet);
        }

        return $changeSets;
    }

    /**
     * @param ReflectionClass<object> $reflectionClass
     */
    private function isMasked(ReflectionClass $reflectionClass, string $propertyName): bool
    {
        try {
            $reflection = $reflectionClass->getProperty($propertyName);
        } catch (Throwable) {
            return false;
        }

        return count($reflection->getAttributes(MaskField::class)) > 0;
    }
}
