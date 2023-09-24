<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class RequestBody
{
    /**
     * @param string[] $context
     * @param string[] $validationGroups
     */
    public function __construct(
        public array $context = [],
        public bool $validate = true,
        public array $validationGroups = []
    ) {
    }
}
