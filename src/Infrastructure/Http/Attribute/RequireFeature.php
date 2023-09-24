<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Attribute;

use Attribute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class RequireFeature extends ConfigurationAnnotation
{
    public function __construct(public string $name)
    {
        parent::__construct([]);
    }

    public function getAliasName(): string
    {
        return 'require_feature';
    }

    public function allowArray(): bool
    {
        return true;
    }
}
