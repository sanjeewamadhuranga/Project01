<?php

declare(strict_types=1);

namespace App\Infrastructure\DataGrid\Configuration;

use App\Application\DataGrid\Exception\InvalidFiltersProvidedException;
use App\Application\DataGrid\Filters\Filters;
use App\Domain\DataGrid\Filters\BasicFilters;
use App\Domain\Document\Configuration\Bank;
use App\Infrastructure\DataGrid\AbstractMongoDataGrid;
use App\Infrastructure\Doctrine\Criteria\Regex;
use App\Infrastructure\Repository\Configuration\BankRepository;
use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * @extends AbstractMongoDataGrid<BasicFilters, Bank>
 */
class BankList extends AbstractMongoDataGrid
{
    public function __construct(private readonly BankRepository $bankRepository)
    {
    }

    protected function getQuery(): Builder
    {
        return $this->bankRepository->notDeletedQueryBuilder();
    }

    public function getFilterDto(): string
    {
        return BasicFilters::class;
    }

    protected function applyFilters(Builder $query, Filters $filters): Builder
    {
        if (!$filters instanceof BasicFilters) {
            throw new InvalidFiltersProvidedException(BasicFilters::class, $filters);
        }

        if (null !== $filters->getSearch()) {
            $searchRegex = Regex::contains($filters->getSearch());

            $query->addOr(
                $query->expr()->field('bankName')->equals($searchRegex),
                $query->expr()->field('bankCode')->equals($searchRegex),
                $query->expr()->field('city')->equals($searchRegex),
                $query->expr()->field('country')->equals($searchRegex),
                $query->expr()->field('branches.branchName')->equals($searchRegex),
                $query->expr()->field('branches.branchCode')->equals($searchRegex)
            );
        }

        return $query;
    }

    public function getSortMap(): array
    {
        return [
            'bankName' => 'bankName',
            'bankCode' => 'bankCode',
            'city' => 'branches.city',
            'country' => 'country',
        ];
    }
}
