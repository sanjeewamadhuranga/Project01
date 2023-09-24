<?php

declare(strict_types=1);

namespace App\Domain\Document\Snapshots;

use App\Domain\Document\BaseDocument;
use App\Domain\Document\Traits\FlowTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\EmbeddedDocument]
class FlowSnapshot extends BaseDocument
{
    use FlowTrait;
}
