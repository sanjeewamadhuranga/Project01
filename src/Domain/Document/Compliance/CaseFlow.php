<?php

declare(strict_types=1);

namespace App\Domain\Document\Compliance;

use App\Domain\Document\BaseDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;

#[MongoDB\EmbeddedDocument]
class CaseFlow extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $reviewComments = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $approveComments = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $suggestedAction = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $merchantFundsAction = null;

    public function getReviewComments(): ?string
    {
        return $this->reviewComments;
    }

    public function setReviewComments(?string $reviewComments): void
    {
        $this->reviewComments = $reviewComments;
    }

    public function getApproveComments(): ?string
    {
        return $this->approveComments;
    }

    public function setApproveComments(?string $approveComments): void
    {
        $this->approveComments = $approveComments;
    }

    public function getSuggestedAction(): ?string
    {
        return $this->suggestedAction;
    }

    public function setSuggestedAction(?string $suggestedAction): void
    {
        $this->suggestedAction = $suggestedAction;
    }

    public function getMerchantFundsAction(): ?string
    {
        return $this->merchantFundsAction;
    }

    public function setMerchantFundsAction(?string $merchantFundsAction): void
    {
        $this->merchantFundsAction = $merchantFundsAction;
    }
}
