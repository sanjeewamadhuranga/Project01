<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Controller\CRUD\CreateAction;
use App\Http\Controller\CRUD\DeleteAction;
use App\Http\Controller\CRUD\IndexAction;
use App\Http\Controller\CRUD\ListAction;
use App\Http\Controller\CRUD\ShowAction;
use App\Http\Controller\CRUD\UpdateAction;

/**
 * Simple base CRUD controller which implements all actions: list, show, delete, create and update.
 * If you need to override or disable any of them, use {@see BasicCrudController} and add only actions you need.
 *
 * @template T of \App\Domain\Document\BaseDocument
 *
 * @extends BasicCrudController<T>
 */
abstract class CrudController extends BasicCrudController
{
    use IndexAction;
    use ListAction;
    use ShowAction;
    use DeleteAction;
    use CreateAction;
    use UpdateAction;
}
