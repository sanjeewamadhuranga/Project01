<?php

declare(strict_types=1);

namespace App\Application\Validation\Company;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class PhoneNumber extends Constraint
{
    public string $message = 'Invalid phone number. Please make sure it starts with plus (+) sign and does not contain any special characters.';
}
