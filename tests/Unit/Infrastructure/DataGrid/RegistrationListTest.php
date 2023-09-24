<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\Registration;
use App\Domain\Document\Flow\Flow;
use App\Domain\Document\Invitation;
use App\Infrastructure\DataGrid\RegistrationList;
use App\Infrastructure\Repository\Company\CompanyRepository;
use App\Infrastructure\Repository\Company\RegistrationRepository;
use App\Tests\Unit\UnitTestCase;
use DateTime;

class RegistrationListTest extends UnitTestCase
{
    public function testItTransformsRegistrationIntoArray(): void
    {
        $companyId = 'company-id-abh5';
        $companyName = 'some-other-company-name';

        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn($companyId);
        $company->method('getTradingName')->willReturn($companyName);

        $invitationId = 'gg45-as33-gl55-as11';
        $invitation = $this->createStub(Invitation::class);
        $invitation->method('getId')->willReturn($invitationId);

        $flowName = 'flow-name';
        $flow = $this->createStub(Flow::class);
        $flow->method('getName')->willReturn($flowName);

        $id = '61f021dc44ade122460c47ae';
        $createdAt = new DateTime();
        $sub = 'registration-sub';
        $phoneNumber = '555-666-777';
        $currentSection = 'registration-current-section';
        $currentScreen = 'registration-current-screen';

        $registration = $this->createStub(Registration::class);
        $registration->method('getId')->willReturn($id);
        $registration->method('getCreatedAt')->willReturn($createdAt);
        $registration->method('getSub')->willReturn($sub);
        $registration->method('getPhoneNumber')->willReturn($phoneNumber);
        $registration->method('getFlow')->willReturn($flow);
        $registration->method('getCurrentSection')->willReturn($currentSection);
        $registration->method('getCurrentScreen')->willReturn($currentScreen);
        $registration->method('getInvitation')->willReturn($invitation);
        $registration->method('getCompany')->willReturn($company);

        $registrationList = new RegistrationList($this->createStub(RegistrationRepository::class), $this->createStub(CompanyRepository::class));

        self::assertSame([
            'id' => $id,
            'createdAt' => $createdAt,
            'sub' => $sub,
            'phoneNumber' => $phoneNumber,
            'flow' => $flow,
            'currentSection' => $currentSection,
            'currentScreen' => $currentScreen,
            'invitation' => $invitationId,
            'companyId' => $companyId,
            'companyName' => $companyName,
            'completed' => false,
        ], $registrationList->transform($registration, 0));
    }
}
