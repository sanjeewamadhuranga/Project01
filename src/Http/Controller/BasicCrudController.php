<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\TransformableGrid;
use App\Domain\DataGrid\Filters\BasicFilters;
use App\Domain\Document\BaseDocument;
use App\Infrastructure\DataGrid\DynamicMongoDataGrid;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Simple base CRUD controller automating common tasks in easy and flexible way.
 * This controller does not implement any actions - use traits in CRUD namespace to add them according to your needs.
 *
 * @see CrudController which implements all the actions
 *
 * @template T of BaseDocument
 */
abstract class BasicCrudController extends BaseController
{
    protected function getNewInstance(): BaseDocument
    {
        return new (static::getItemClass())();
    }

    /**
     * @return T
     */
    protected function find(string $id): BaseDocument
    {
        $item = $this->getDoctrineMongoDb()->getManager()->find(static::getItemClass(), $id);

        if (null === $item) {
            throw new NotFoundHttpException();
        }

        return $item;
    }

    /**
     * @return TransformableGrid<BasicFilters, T>
     */
    protected function getList(): DataGrid
    {
        /** @var DocumentManager $dm */
        $dm = $this->getDoctrineMongoDb()->getManager();

        return new DynamicMongoDataGrid($dm, static::getItemClass(), fn ($item) => $item);
    }

    /**
     * An unique key used to generate route, template and permission prefixes.
     * Use "dot" notation keys: "test.admin". Defaults to the item class name converted to camel_case.
     */
    protected static function getKey(): string
    {
        return InflectorFactory::create()->build()->tableize(basename(str_replace('\\', '/', static::getItemClass())));
    }

    /**
     * Permission prefix used to authorize user to CRUD actions.
     * Defaults to the CRUD key.
     */
    protected static function getPermissionPrefix(): string
    {
        return trim(static::getKey(), '.');
    }

    /**
     * Route prefix used to generate the routes.
     * Defaults to the CRUD key with dots replaced with underscores.
     */
    protected static function getRoutePrefix(): string
    {
        return str_replace('.', '_', static::getKey());
    }

    /**
     * Template prefix used to generate the template paths.
     * Defaults to the CRUD key with dots replaced with slashes.
     */
    protected static function getTemplatePrefix(): string
    {
        return str_replace('.', '/', static::getKey());
    }

    /**
     * Form type to use in create/edit actions.
     *
     * @return class-string<FormTypeInterface>
     */
    protected static function getFormType(): string
    {
        return FormType::class;
    }

    /**
     * Item class name.
     *
     * @return class-string<T>
     */
    abstract protected static function getItemClass(): string;
}
