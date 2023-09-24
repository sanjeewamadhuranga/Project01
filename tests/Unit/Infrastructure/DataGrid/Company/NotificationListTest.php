<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Company;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Notification\AbstractNotification;
use App\Infrastructure\DataGrid\Notifications\NotificationList;
use App\Tests\Unit\UnitTestCase;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class NotificationListTest extends UnitTestCase
{
    public function testItTransformsNotificationIntoArray(): void
    {
        $id = '61f0213b6c0d85172231b50c';
        $sub = '576f383c-bc03-4a64-b04e-d0f458f7da89';
        $companyId = '62b55841b35838afe5dce99c';
        $companyName = 'Test Company';
        $title = 'notification title';
        $message = 'some message';
        $isSent = false;
        $metadata = ['test' => 'response'];
        $createdAt = new DateTimeImmutable();

        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn($companyId);
        $company->method('__toString')->willReturn($companyName);

        $notification = $this->createStub(AbstractNotification::class);
        $notification->method('getId')->willReturn($id);
        $notification->method('getSub')->willReturn($sub);
        $notification->method('getCompany')->willReturn($company);
        $notification->method('getTitle')->willReturn($title);
        $notification->method('getMessage')->willReturn($message);
        $notification->method('isSent')->willReturn($isSent);
        $notification->method('getMeta')->willReturn($metadata);
        $notification->method('getCreatedAt')->willReturn($createdAt);

        $notificationList = new NotificationList($this->createStub(DocumentRepository::class));

        self::assertSame([
            'id' => $id,
            'companyId' => $companyId,
            'companyName' => $companyName,
            'sub' => $sub,
            'title' => $title,
            'message' => $message,
            'sent' => $isSent,
            'metadata' => $metadata,
            'createdAt' => $createdAt,
        ], $notificationList->transform($notification, 0));
    }
}
