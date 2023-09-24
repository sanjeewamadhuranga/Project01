<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Compliance;

use App\Application\DataGrid\Exception\InvalidFiltersProvidedException;
use App\Application\DataGrid\Filters\Filters;
use App\Domain\DataGrid\Filters\DisputeFilters;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Compliance\Dispute;
use App\Domain\Transformer\DisputeTransformer;
use App\Infrastructure\DataGrid\AbstractMongoDataGrid;
use App\Infrastructure\Repository\Company\CompanyRepository;
use App\Infrastructure\Repository\DisputeRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * @extends AbstractMongoDataGrid<DisputeFilters, Dispute>
 */
class DisputeList extends AbstractMongoDataGrid
{
    public function __construct(private readonly DisputeRepository $repository, private readonly CompanyRepository $companyRepository, private readonly DisputeTransformer $transformer)
    {
    }

    protected function getQuery(): Builder
    {
        return $this->repository->createQueryBuilder();
    }

    protected function applyFilters(Builder $query, Filters $filters): Builder
    {
        if (!$filters instanceof DisputeFilters) {
            throw new InvalidFiltersProvidedException(DisputeFilters::class, $filters);
        }

        if (count($filters->getStatus()) > 0) {
            $query->field('state')->in($filters->getStatus());
        }

        if (null !== $filters->getSearch()) {
            $companyIds = array_map(
                static fn (Company $company) => $company->getId(),
                [...$this->companyRepository->findByName($filters->getSearch())]
            );
            $query->field('company')->in($companyIds);
        }

        return $query;
    }

    public function getFilterDto(): string
    {
        return DisputeFilters::class;
    }

    public function transform(mixed $item, int|string $index): mixed
    {
        return $this->transformer->transform($item);
    }

    public function getSortMap(): array
    {
        return [
            'id' => 'id',
            'state' => 'state',
            'createdAt' => 'id',
        ];
    }
}
