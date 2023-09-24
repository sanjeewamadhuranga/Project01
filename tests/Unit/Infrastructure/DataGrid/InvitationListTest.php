<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Invitation;
use App\Infrastructure\DataGrid\InvitationList;
use App\Infrastructure\Repository\Company\CompanyRepository;
use App\Infrastructure\Repository\InvitationRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;

class InvitationListTest extends UnitTestCase
{
    public function testItTransformsInvitationIntoArray(): void
    {
        $companyId = 'company-id-abg5';
        $companyName = 'some-company-name';

        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn($companyId);
        $company->method('getTradingName')->willReturn($companyName);

        $id = '61f021a163b571290822cddf';
        $open = true;
        $expires = new DateTime();
        $invitationCode = 'invitation-code';
        $email = 'invitation@pay.com';
        $emailHash = md5($email);

        $invitation = $this->createStub(Invitation::class);
        $invitation->method('getId')->willReturn($id);
        $invitation->method('isOpen')->willReturn($open);
        $invitation->method('getExpires')->willReturn($expires);
        $invitation->method('getInvitationCode')->willReturn($invitationCode);
        $invitation->method('getEmail')->willReturn($email);
        $invitation->method('getEmailHash')->willReturn($emailHash);
        $invitation->method('getCompany')->willReturn($company);

        $invitationList = new InvitationList($this->createStub(InvitationRepository::class), $this->createStub(CompanyRepository::class));

        self::assertSame([
            'id' => $id,
            'open' => $open,
            'expires' => $expires,
            'invitationCode' => $invitationCode,
            'email' => $email,
            'emailHash' => $emailHash,
            'companyId' => $companyId,
            'companyName' => $companyName,
        ], $invitationList->transform($invitation, 0));
    }
}
