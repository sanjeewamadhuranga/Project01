<?php

declare(strict_types=1);

namespace App\Http\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

class EnableTwoFactorRequest
{
    #[Assert\NotBlank]
    public string $code = '';
}
