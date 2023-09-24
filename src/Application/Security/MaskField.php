<?php

declare(strict_types=1);

namespace App\Application\Security;

use Attribute;

/**
 * Hide the actual value with placeholder when creating an activity log entry. Used to keep secret values safe.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MaskField
{
    final public const PLACEHOLDER = '*hidden*';
}
