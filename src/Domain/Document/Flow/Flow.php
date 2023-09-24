<?php

declare(strict_types=1);

namespace App\Domain\Document\Flow;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Snapshots\FlowSnapshot;
use App\Domain\Document\Traits\FlowTrait;
use App\Infrastructure\Repository\FlowRepository;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[Unique(fields: ['key'])]
#[MongoDB\Document(collection: 'registration_flows', repositoryClass: FlowRepository::class)]
class Flow extends BaseDocument
{
    use FlowTrait;

    public function getSnapShot(): FlowSnapshot
    {
        $flowSnapshot = new FlowSnapshot();
        $flowSnapshot->setName($this->getName());
        $flowSnapshot->setKey($this->getKey());
        $flowSnapshot->setDefault($this->isDefault());
        $flowSnapshot->setSections($this->getSections());

        return $flowSnapshot;
    }
}
