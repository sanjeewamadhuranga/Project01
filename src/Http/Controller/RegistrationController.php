<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\Security\Permissions\Permission;
use App\Domain\Document\Company\Registration;
use App\Http\Controller\CRUD\DeleteAction;
use App\Http\Controller\CRUD\IndexAction;
use App\Http\Controller\CRUD\ListAction;
use App\Infrastructure\DataGrid\RegistrationList;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @extends BasicCrudController<Registration>
 */
#[Route('/onboarding/registration', name: 'onboarding_registration_')]
class RegistrationController extends BasicCrudController
{
    use IndexAction;
    use ListAction;
    use DeleteAction;

    public function __construct(private readonly RegistrationList $list)
    {
    }

    protected function getList(): RegistrationList // @phpstan-ignore-line
    {
        return $this->list;
    }

    protected static function getItemClass(): string
    {
        return Registration::class;
    }

    public static function getKey(): string
    {
        return 'onboarding.registration';
    }

    public static function getPermissionPrefix(): string
    {
        return rtrim(Permission::ONBOARDING_REGISTRATIONS, '.');
    }
}
