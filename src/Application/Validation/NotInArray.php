<?php

declare(strict_types=1);

namespace App\Application\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class NotInArray extends Constraint
{
    public string $message = 'This value is already used.';

    /**
     * @param list<mixed>               $choices
     * @param array<string>|null        $groups
     * @param array<string, mixed>|null $payload
     */
    public function __construct(public array $choices, array $groups = null, array $payload = null)
    {
        parent::__construct([], $groups, $payload);
    }
}
