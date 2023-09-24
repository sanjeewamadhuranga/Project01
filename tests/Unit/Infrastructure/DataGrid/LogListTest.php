<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Document\Log\Details;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\DataGrid\LogList;
use App\Infrastructure\Repository\LogRepository;
use App\Infrastructure\Repository\Security\UserRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class LogListTest extends UnitTestCase
{
    public function testItTransformsLogIntoArray(): void
    {
        $userEmail = 'user@pay.com';
        $user = $this->createStub(Administrator::class);
        $user->method('getEmail')->willReturn($userEmail);

        $id = '61f021a163b571290822cde0';
        $type = 'log-type';
        $createdAt = new DateTime();
        $objectClass = 'object-class';
        $objetId = 'hh55-gg44-ff33-dd22-aa11';
        $details = $this->createStub(Details::class);
        $changeSets = new ArrayCollection(['aaa', 'bbb', 'ccc']);

        $log = $this->createStub(Log::class);
        $log->method('getId')->willReturn($id);
        $log->method('getType')->willReturn($type);
        $log->method('getUser')->willReturn($user);
        $log->method('getCreatedAt')->willReturn($createdAt);
        $log->method('getObjectClass')->willReturn($objectClass);
        $log->method('getObjectId')->willReturn($objetId);
        $log->method('getDetails')->willReturn($details);
        $log->method('getChangeSets')->willReturn($changeSets);

        $logList = new LogList($this->createStub(LogRepository::class), $this->createStub(UserRepository::class));

        self::assertSame([
            'id' => $id,
            'action' => $type,
            'user' => $userEmail,
            'created' => $createdAt,
            'objectClass' => $objectClass,
            'objectId' => $objetId,
            'details' => $details,
            'changeSets' => $changeSets,
        ], $logList->transform($log, 0));
    }
}
