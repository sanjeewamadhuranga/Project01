<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Infrastructure\Repository\ComplianceFileRepository;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[MongoDB\Document(collection: 'compliance_files', repositoryClass: ComplianceFileRepository::class)]
class ComplianceFile extends BaseDocument implements CompanyAware
{
    #[Assert\NotBlank]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    private string $name;

    #[Vich\UploadableField(mapping: 'compliance_files', fileNameProperty: 'key')]
    #[Assert\File(maxSize: '32M', mimeTypes: [
        'image/jpeg',
        'image/png',
        'image/bmp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/csv',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])]
    private ?File $document = null;

    #[Gedmo\Blameable(on: 'create')]
    #[MongoDB\Field(type: MongoDBType::STRING)]
    private ?string $uploader = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    private ?string $key = null;

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class)]
    private Company $company;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUploader(): ?string
    {
        return $this->uploader;
    }

    public function setUploader(?string $uploader): void
    {
        $this->uploader = $uploader;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): void
    {
        $this->key = $key;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function setDocument(?File $document = null): void
    {
        $this->document = $document;

        if (null !== $document) {
            $this->updatedAt = new DateTimeImmutable();
            $this->createdAt = new DateTimeImmutable();
        }
    }

    public function getDocument(): ?File
    {
        return $this->document;
    }
}
