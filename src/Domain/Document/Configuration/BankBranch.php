<?php

declare(strict_types=1);

namespace App\Domain\Document\Configuration;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[MongoDB\EmbeddedDocument]
class BankBranch implements Stringable
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $branchCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $branchName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $city = null;

    public function getBranchCode(): ?string
    {
        return $this->branchCode;
    }

    public function setBranchCode(?string $branchCode): void
    {
        $this->branchCode = $branchCode;
    }

    public function getBranchName(): ?string
    {
        return $this->branchName;
    }

    public function setBranchName(?string $branchName): void
    {
        $this->branchName = $branchName;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getLabel(): string
    {
        return sprintf('%s (%s)', $this->getBranchName(), $this->getBranchCode());
    }

    public function __toString(): string
    {
        return (string) $this->branchName;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (null !== $this->getBranchCode() && str_contains($this->getBranchCode(), '.')) {
            $context->buildViolation('Dot is not allowed.')
                ->atPath('branchCode')
                ->addViolation();
        }
    }
}
