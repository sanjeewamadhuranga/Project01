<?php

declare(strict_types=1);

namespace App\Domain\Document\Security;

use App\Domain\Document\BaseDocument;
use App\Infrastructure\Repository\Security\ManagerPortalRoleRepository;
use App\Infrastructure\Validator\ManagerPortalRolePermission;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Unique(fields: ['name'])]
#[ManagerPortalRolePermission]
#[MongoDB\Document(collection: 'manager_portal_role', repositoryClass: ManagerPortalRoleRepository::class)]
class ManagerPortalRole extends BaseDocument implements Stringable, NormalizableInterface
{
    #[Assert\Type(MongoDBType::STRING)]
    #[Assert\Length(min: 2, max: 100)]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $name;

    #[Assert\Type(MongoDBType::STRING)]
    #[Assert\Length(max: 250)]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $description;

    #[MongoDB\ReferenceOne(storeAs: 'dbRefWithDb', targetDocument: ManagerPortalRole::class)]
    protected ?ManagerPortalRole $protectedByRole = null;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $permissions = [];

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: 'collection')]
    protected array $newPermissions = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param string[] $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * @return string[]
     */
    public function getNewPermissions(): array
    {
        return $this->newPermissions;
    }

    /**
     * @param string[] $newPermissions
     */
    public function setNewPermissions(array $newPermissions): void
    {
        $this->newPermissions = $newPermissions;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getProtectedByRole(): ?ManagerPortalRole
    {
        return $this->protectedByRole;
    }

    public function setProtectedByRole(?ManagerPortalRole $protectedByRole): void
    {
        $this->protectedByRole = $protectedByRole;
    }

    public function normalize(NormalizerInterface $normalizer, string $format = null, array $context = []): array|string|int|float|bool
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'protectedByRole' => $this->getProtectedByRole()?->getName(),
            'deleted' => $this->isDeleted(),
        ];
    }
}
