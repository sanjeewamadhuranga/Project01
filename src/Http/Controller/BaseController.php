<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Application\DataGrid\DataGrid;
use App\Application\DataGrid\DataGridHandlerInterface;
use App\Domain\Document\Security\Administrator;
use App\Domain\Settings\Features;
use App\Domain\Settings\SystemSettings;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\Query\Builder;
use Pagerfanta\Doctrine\MongoDBODM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
                'doctrine_mongodb' => ManagerRegistry::class,
                'translator' => TranslatorInterface::class,
                'settings' => SystemSettings::class,
                'features' => Features::class,
                DataGridHandlerInterface::class => DataGridHandlerInterface::class,
                HubInterface::class => HubInterface::class,
            ];
    }

    /**
     * @template TFilters of \App\Application\DataGrid\Filters\Filters
     *
     * @param DataGrid<TFilters> $dataGrid
     */
    protected function handleDataGrid(Request $request, DataGrid $dataGrid): Response
    {
        return $this->container->get(DataGridHandlerInterface::class)($request, $dataGrid);
    }

    protected function getDoctrineMongoDb(): ManagerRegistry
    {
        return $this->container->get('doctrine_mongodb');
    }

    /**
     * @return PagerfantaInterface<mixed>
     */
    protected function paginate(Request $request, Builder $query): PagerfantaInterface
    {
        $paginatorAdapter = new QueryAdapter($query);

        return (new Pagerfanta($paginatorAdapter))
            ->setMaxPerPage(max(1, $request->query->getInt('limit', 50)))
            ->setCurrentPage(max(1, $request->query->getInt('page', 1)));
    }

    /**
     * @param array<string, string> $parameters
     */
    protected function trans(?string $message, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->container->get('translator')->trans($message, $parameters, $domain, $locale);
    }

    public function getUser(): Administrator
    {
        $user = parent::getUser();

        if ($user instanceof Administrator) {
            return $user;
        }

        throw new AccessDeniedException();
    }
}
