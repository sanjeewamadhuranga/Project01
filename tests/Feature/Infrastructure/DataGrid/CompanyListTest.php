<?php

declare(strict_types=1);

namespace App\Tests\Feature\Infrastructure\DataGrid;

use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Application\DataGrid\Pagination\LengthAwarePaginationInfo;
use App\Application\DataGrid\TransformableGrid;
use App\Domain\Company\Status;
use App\Domain\DataGrid\Filters\CompanyFilters;
use App\Domain\Document\Company\Company;
use App\Infrastructure\DataGrid\Company\CompanyList;
use Traversable;

class CompanyListTest extends BaseDataGridTest
{
    public function testApplyEmptyFilters(): void
    {
        $filters = new CompanyFilters();
        $data = $this->list->getData($this->getRequestForFilters($filters));
        $paginationInfo = $data->getPaginationInfo();
        self::assertInstanceOf(LengthAwarePaginationInfo::class, $paginationInfo);
        self::assertSame(101, $paginationInfo->getFilteredCount());
    }

    public function testApplyStatusFilters(): void
    {
        $filters = new CompanyFilters();
        $filters->setStatus(Status::PENDING);
        $data = $this->list->getData($this->getRequestForFilters($filters));
        $paginationInfo = $data->getPaginationInfo();
        self::assertInstanceOf(LengthAwarePaginationInfo::class, $paginationInfo);
        self::assertSame(25, $paginationInfo->getFilteredCount());
    }

    public function testSearchByName(): void
    {
        $filters = new CompanyFilters();
        $company = $this->getTestCompany();
        $filters->setSearchMerchant($company->getId());
        $data = $this->list->getData($this->getRequestForFilters($filters));
        $paginationInfo = $data->getPaginationInfo();
        $items = $data->getItems();
        self::assertInstanceOf(LengthAwarePaginationInfo::class, $paginationInfo);
        self::assertSame(1, $paginationInfo->getFilteredCount());
        self::assertSame($company->getId(), current([...$items])->getId());

        $filters->setMerchantId($company->getId());
        $data = $this->list->getData($this->getRequestForFilters($filters));
        $paginationInfo = $data->getPaginationInfo();
        $items = $data->getItems();
        self::assertInstanceOf(LengthAwarePaginationInfo::class, $paginationInfo);
        self::assertSame(1, $paginationInfo->getFilteredCount());
        self::assertSame($company->getId(), current([...$items])->getId());
    }

    public function testSortByStatus(): void
    {
        $companies = $this->list->getData(new GridRequest(null, new Sorting('status', SortDirection::ASC)))->getItems();
        self::assertInstanceOf(Traversable::class, $companies);
        /** @var Company $firstCompany */
        $firstCompany = current(iterator_to_array($companies));

        self::assertSame(Status::TERMINATED, $firstCompany->getStatus());
    }

    protected function getDataGrid(): TransformableGrid
    {
        return self::$client->getContainer()->get(CompanyList::class); // @phpstan-ignore-line
    }
}
