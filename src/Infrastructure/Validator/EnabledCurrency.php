<?php

declare(strict_types=1);

namespace App\Infrastructure\Validator;

use Attribute;
use Symfony\Component\Validator\Constraints\Currency;

#[Attribute]
class EnabledCurrency extends Currency
{
}
