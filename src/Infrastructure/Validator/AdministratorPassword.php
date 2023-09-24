<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use Attribute;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AdministratorPassword extends Compound
{
    /**
     * @param mixed[] $options
     */
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Length(min: 14),
            new Assert\Regex(pattern: '/\p{Ll}/u', message: 'password.requiresLowercase'),
            new Assert\Regex(pattern: '/\p{Lu}/u', message: 'password.requiresUppercase'),
            new Assert\Regex(pattern: '/\pN/u', message: 'password.requiresDigit'),
            new Assert\Regex(pattern: '/[^\p{Ll}\p{Lu}\pL\pN]/u', message: 'password.requiresSpecial'),
            new Assert\NotCompromisedPassword(),
        ];
    }
}
