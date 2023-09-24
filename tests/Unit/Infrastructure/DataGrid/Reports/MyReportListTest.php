<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Reports;

use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\Pagination;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Domain\DataGrid\Filters\BasicFilters;
use App\Domain\Document\Report;
use App\Infrastructure\DataGrid\Reports\MyReportList;
use App\Infrastructure\Repository\ReportRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MyReportListTest extends UnitTestCase
{
    public function testItTransformsReportIntoArray(): void
    {
        $id = '61f021dc44ade122460c47b1';
        $createdAt = new DateTime();
        $isReady = false;
        $module = 'module';
        $hasError = true;
        $errorMessage = 'error message';

        $report = $this->createStub(Report::class);
        $report->method('getId')->willReturn($id);
        $report->method('getCreatedAt')->willReturn($createdAt);
        $report->method('isReady')->willReturn($isReady);
        $report->method('getModule')->willReturn($module);
        $report->method('isHasError')->willReturn($hasError);
        $report->method('getErrorMessage')->willReturn($errorMessage);

        $reportList = new MyReportList($this->createStub(ReportRepository::class), $this->createStub(TokenStorageInterface::class));

        self::assertSame([
            'id' => $id,
            'createdAt' => $createdAt,
            'isReady' => $isReady,
            'module' => $module,
            'hasError' => $hasError,
            'errorMessage' => $errorMessage,
        ], $reportList->transform($report, 0));
    }

    public function testItThrowsExceptionWhenWrongUserProvided(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        $tokenStorageInterface = $this->createStub(TokenStorageInterface::class);
        $tokenStorageInterface->method('getToken')->willReturn($token);

        $reportList = new MyReportList($this->createStub(ReportRepository::class), $tokenStorageInterface);

        self::expectException(AccessDeniedException::class);
        $reportList->getData(new GridRequest(new BasicFilters(), new Sorting(null, SortDirection::ASC), new Pagination()));
    }
}
