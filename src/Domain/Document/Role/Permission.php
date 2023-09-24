<?php

declare(strict_types=1);

namespace App\Domain\Document\Role;

use App\Domain\Permission\Module;
use App\Domain\Permission\PermissionOperation;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Stringable;

#[MongoDB\EmbeddedDocument]
class Permission implements Stringable
{
    #[MongoDB\Field(enumType: Module::class)]
    protected ?Module $module = null;

    /**
     * @var PermissionOperation[]
     */
    #[MongoDB\Field(type: 'permission_operations')]
    protected array $allowedOperations = [];

    public static function fullAccess(Module $module): self
    {
        $instance = new self();
        $instance->setModule($module);
        $instance->setAllowedOperations(PermissionOperation::cases());

        return $instance;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): void
    {
        $this->module = $module;
    }

    /**
     * @return PermissionOperation[]
     */
    public function getAllowedOperations(): array
    {
        return $this->allowedOperations;
    }

    /**
     * @param PermissionOperation[] $allowedOperations
     */
    public function setAllowedOperations(array $allowedOperations): void
    {
        $this->allowedOperations = $allowedOperations;
    }

    public function __toString(): string
    {
        return (string) $this->getModule()?->value;
    }
}
