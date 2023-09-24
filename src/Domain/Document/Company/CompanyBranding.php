<?php

declare(strict_types=1);

namespace App\Domain\Document\Company;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Interfaces\CompanyAware;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document(collection: 'companies_branding')]
class CompanyBranding extends BaseDocument implements CompanyAware
{
    #[MongoDB\ReferenceOne(name: 'companyId', storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    protected Company $company;

    /**
     * @var string[]|null
     */
    #[MongoDB\Field(type: 'collection')]
    #[Assert\Count(min: 50)]
    #[Assert\Unique]
    #[Assert\AtLeastOneOf([new Assert\All([new Assert\Regex('/^[a-zA-Z ]+$/')])])]
    protected ?array $securityWordAdjectives = null;

    /**
     * @var string[]|null
     */
    #[MongoDB\Field(type: 'collection')]
    #[Assert\Count(min: 50)]
    #[Assert\Unique]
    #[Assert\AtLeastOneOf([new Assert\All([new Assert\Regex('/^[[a-zA-Z ]+$/')])])]
    protected ?array $securityWordNouns = null;

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string[]|null
     */
    public function getSecurityWordAdjectives(): ?array
    {
        return $this->securityWordAdjectives;
    }

    /**
     * @param string[]|null $securityWordAdjectives
     */
    public function setSecurityWordAdjectives(?array $securityWordAdjectives): void
    {
        $this->securityWordAdjectives = $securityWordAdjectives;
    }

    /**
     * @return string[]|null
     */
    public function getSecurityWordNouns(): ?array
    {
        return $this->securityWordNouns;
    }

    /**
     * @param string[]|null $securityWordNouns
     */
    public function setSecurityWordNouns(?array $securityWordNouns): void
    {
        $this->securityWordNouns = $securityWordNouns;
    }
}
