<?php

declare(strict_types=1);

namespace App\Domain\Document\Configuration;

use App\Domain\Document\BaseDocument;
use App\Infrastructure\Repository\Configuration\BankRepository;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Unique(fields: ['bankCode'])]
#[MongoDB\Document(collection: 'config_banks', repositoryClass: BankRepository::class)]
class Bank extends BaseDocument implements Stringable
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $bankCode = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $bankName = null;

    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected ?string $country = null;

    /**
     * @var Collection<int, BankBranch>
     */
    #[MongoDB\EmbedMany(targetDocument: BankBranch::class)]
    protected Collection $branches;

    public function __construct()
    {
        $this->branches = new ArrayCollection();
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): void
    {
        $this->bankCode = $bankCode;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): void
    {
        $this->bankName = $bankName;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return Collection<int, BankBranch>
     */
    public function getBranches(): Collection
    {
        return $this->branches;
    }

    public function getBranch(string $code): ?BankBranch
    {
        $branch = $this->branches->filter(fn (BankBranch $branch) => $branch->getBranchCode() === $code)->first();

        return false !== $branch ? $branch : null;
    }

    /**
     * @param Collection<int, BankBranch> $branches
     */
    public function setBranches(Collection $branches): void
    {
        $this->branches = $branches;
    }

    public function getLabel(): string
    {
        return sprintf('%s (%s)', $this->getBankName(), $this->getBankCode());
    }

    public function __toString(): string
    {
        return (string) $this->bankName;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (null !== $this->getBankCode() && str_contains($this->getBankCode(), '.')) {
            $context->buildViolation('Dot is not allowed.')
                ->atPath('bankCode')
                ->addViolation();
        }
    }
}
