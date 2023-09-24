<?php

declare(strict_types=1);

namespace App\Http\Model\Request;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Validator\CurrentUserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordConfirmationRequest
{
    #[Assert\NotBlank, CurrentUserPassword]
    public string $password;

    public function __construct(#[Assert\NotBlank] public Administrator $user)
    {
    }
}
