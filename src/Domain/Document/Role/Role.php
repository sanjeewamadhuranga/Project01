<?php

declare(strict_types=1);

namespace App\Domain\Document\Role;

use App\Domain\Company\PushNotificationType;
use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Permission\Module;
use App\Infrastructure\Repository\RoleRepository;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[Unique(fields: ['name'])]
#[MongoDB\Document(collection: 'roles', repositoryClass: RoleRepository::class)]
class Role extends BaseDocument implements Stringable
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $name = '';

    #[Assert\Type(MongoDBType::STRING)]
    #[Assert\Length(max: 250)]
    #[MongoDB\Field(name: 'desc', type: MongoDBType::STRING)]
    protected ?string $description = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $userAware = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    protected bool $default = false;

    /**
     * @var Collection<int,Permission>
     */
    #[MongoDB\EmbedMany(targetDocument: Permission::class)]
    protected Collection $permissions;

    /**
     * @var PushNotificationType[]
     */
    #[MongoDB\Field(type: 'push_notification_types')]
    protected array $allowedPushNotifications = [];

    /**
     * @var Collection<int, Company>
     */
    #[MongoDB\ReferenceMany(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected Collection $companies;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->companies = new ArrayCollection();
    }

    public static function withAllModules(): self
    {
        $role = new self();

        foreach (Module::cases() as $module) {
            $role->permissions[] = Permission::fullAccess($module);
        }

        return $role;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isUserAware(): bool
    {
        return $this->userAware;
    }

    public function setUserAware(bool $userAware): void
    {
        $this->userAware = $userAware;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    /**
     * @return Collection<int,Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * @param Collection<int,Permission> $permissions
     */
    public function setPermissions(Collection $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function addPermission(Permission $permission): void
    {
        $this->permissions[] = $permission;
    }

    /**
     * @return PushNotificationType[]
     */
    public function getAllowedPushNotifications(): array
    {
        return $this->allowedPushNotifications;
    }

    /**
     * @param PushNotificationType[] $allowedPushNotifications
     */
    public function setAllowedPushNotifications(array $allowedPushNotifications): void
    {
        $this->allowedPushNotifications = $allowedPushNotifications;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /**
     * @param Collection<int, Company> $companies
     */
    public function setCompanies(Collection $companies): void
    {
        $this->companies = $companies;
    }

    public function addCompany(Company $company): void
    {
        $this->companies[] = $company;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
