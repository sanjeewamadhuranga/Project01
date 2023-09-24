<?php

declare(strict_types=1);

namespace App\Domain\Document\Compliance;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Infrastructure\Repository\FraudSignalRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\Document(collection: 'compliance_fraud_signals', repositoryClass: FraudSignalRepository::class)]
class FraudSignal extends BaseDocument implements CompanyAware
{
    #[MongoDB\Field(type: MongoDBType::INT)]
    public int $riskScoreLimit = 100;

    #[MongoDB\Field(type: MongoDBType::INT, nullable: true)]
    public ?int $maxTransactionAmount = null;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    public bool $issuingCountryCompanyMismatch = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    public bool $issuingCountryUserLocationMismatch = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    public bool $issuingCountryBillingMismatch = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    public bool $addressVerificationFailure = false;

    #[MongoDB\Field(type: MongoDBType::BOOL)]
    public bool $addressVerificationUnknown = false;

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::COLLECTION)]
    public array $restrictedBillingCountries = [];

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::COLLECTION)]
    public array $restrictedUserLocationCountries = [];

    /**
     * @var string[]
     */
    #[MongoDB\Field(type: MongoDBType::COLLECTION)]
    public array $restrictedIssuingCountries = [];

    #[MongoDB\ReferenceOne(storeAs: ClassMetadata::REFERENCE_STORE_AS_ID, targetDocument: Company::class, inversedBy: 'fraudSignal')]
    public Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
