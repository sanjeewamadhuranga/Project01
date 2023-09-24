<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Reports;

use App\Domain\Document\AutoCredit;
use App\Infrastructure\DataGrid\Reports\AutoCreditList;
use App\Infrastructure\Repository\AutoCreditRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;

class AutoCreditListTest extends UnitTestCase
{
    public function testItTransformsAutoCreditIntoArray(): void
    {
        $id = '61f021dc44ade122460c47b0';
        $createdAt = new DateTime();
        $processingDate = 'some date';
        $fileName = 'filename.jpg';
        $amount = '40000';
        $itemIds = [1, 2, 3, 4];

        $autoCredit = $this->createStub(AutoCredit::class);
        $autoCredit->method('getId')->willReturn($id);
        $autoCredit->method('getCreatedAt')->willReturn($createdAt);
        $autoCredit->method('getProcessingDate')->willReturn($processingDate);
        $autoCredit->method('getFilename')->willReturn($fileName);
        $autoCredit->method('getTotalAmount')->willReturn($amount);
        $autoCredit->method('getAutocreditItemIds')->willReturn($itemIds);

        $autoCreditList = new AutoCreditList($this->createStub(AutoCreditRepository::class));

        self::assertSame([
            'id' => $id,
            'created' => $createdAt,
            'processingDate' => $processingDate,
            'filename' => $fileName,
            'totalAmount' => $amount,
            'totalLineItems' => count($itemIds),
        ], $autoCreditList->transform($autoCredit, 0));
    }
}
