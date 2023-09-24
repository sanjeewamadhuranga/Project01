<?php

declare(strict_types=1);

namespace App\Http\Model\Request;

use App\Infrastructure\Validator\AdministratorPassword;
use App\Infrastructure\Validator\CurrentUserPassword;
use App\Infrastructure\Validator\NotUsedBeforePassword;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordRequest
{
    #[Assert\NotBlank, CurrentUserPassword]
    public string $currentPassword = '';

    #[AdministratorPassword, NotUsedBeforePassword, Assert\NotEqualTo(propertyPath: 'currentPassword', message: 'You can\'t use same password')]
    public string $newPassword = '';
}
