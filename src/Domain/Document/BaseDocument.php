<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\Document\Interfaces\SoftDeleteable;
use App\Domain\Document\Traits\HasId;
use App\Domain\Document\Traits\SoftDeleteableTrait;
use App\Domain\Document\Traits\TimestampableTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\MappedSuperclass]
abstract class BaseDocument implements SoftDeleteable
{
    use HasId;
    use TimestampableTrait;
    use SoftDeleteableTrait;
}
