<?php

declare(strict_types=1);

namespace App\Domain\Document\Compliance;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Security\Administrator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type as MongoDBType;
use Gedmo\Mapping\Annotation as Gedmo;

#[MongoDB\EmbeddedDocument]
class DisputeNote extends BaseDocument
{
    #[MongoDB\Field(type: MongoDBType::STRING)]
    protected string $detail;

    #[Gedmo\Blameable(on: 'create')]
    #[MongoDB\ReferenceOne(targetDocument: Administrator::class)]
    protected Administrator $user;

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): void
    {
        $this->detail = $detail;
    }

    public function getUser(): Administrator
    {
        return $this->user;
    }

    public function setUser(Administrator $user): void
    {
        $this->user = $user;
    }
}
