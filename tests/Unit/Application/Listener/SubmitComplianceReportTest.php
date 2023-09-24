<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener;

use App\Application\Compliance\Onfido;
use App\Application\Listener\SubmitComplianceReport;
use App\Domain\Document\Company\Address;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\ComplianceReport;
use App\Domain\Document\Company\User;
use App\Domain\Event\Company\UserCreated;
use App\Domain\Settings\Config;
use App\Domain\Settings\Features;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class SubmitComplianceReportTest extends UnitTestCase
{
    private SubmitComplianceReport $subscriber;

    private Features&MockObject $features;

    private Onfido&MockObject $onfido;

    protected function setUp(): void
    {
        parent::setUp();

        $this->features = $this->createMock(Features::class);
        $config = $this->createMock(Config::class);
        $config->expects(self::atLeastOnce())->method('getFeatures')->willReturn($this->features);
        $this->onfido = $this->createMock(Onfido::class);
        $this->subscriber = new SubmitComplianceReport($this->onfido, $config);
    }

    public function testItNoopsWhenKycIsDisabled(): void
    {
        $this->features->expects(self::once())->method('isKycEnabled')->willReturn(false);
        $this->onfido->expects(self::never())->method('createApplicant');
        $this->subscriber->__invoke(
            new UserCreated($this->createStub(Company::class), $this->createStub(User::class))
        );
    }

    public function testItNoopsWhenUserDoesNotHaveAddressAndKycIsRequested(): void
    {
        $this->features->expects(self::once())->method('isKycEnabled')->willReturn(true);
        $this->onfido->expects(self::never())->method('createApplicant');
        $this->subscriber->__invoke(
            new UserCreated($this->createStub(Company::class), $this->createStub(User::class))
        );
    }

    public function testItNoopsWhenUserDoesNotHasAddressWhenKycIsNotRequested(): void
    {
        $this->features->expects(self::once())->method('isKycEnabled')->willReturn(true);
        $this->onfido->expects(self::never())->method('createApplicant');
        $user = $this->createMock(User::class);
        $user
            ->method('getAddresses')
            ->willReturn($this->createStub(Address::class));

        $user->expects(self::once())->method('isRequireKyc')
            ->willReturn(false);

        $this->subscriber->__invoke(new UserCreated($this->createStub(Company::class), $user));
    }

    public function testItCreatesApplicantAndSetsApplicantIdToComplianceReport(): void
    {
        $this->features->expects(self::once())->method('isKycEnabled')->willReturn(true);
        $user = $this->createMock(User::class);
        $user->expects(self::once())
            ->method('getAddresses')
            ->willReturn($this->createStub(Address::class));
        $user->expects(self::once())
            ->method('setComplianceReport')
            ->with(self::callback(fn (ComplianceReport $report) => 'test-applicant-id' === $report->getApplicantId()));
        $user->expects(self::once())->method('isRequireKyc')
            ->willReturn(true);
        $this->onfido->expects(self::once())->method('createApplicant')->with($user)->willReturn('test-applicant-id');
        $this->subscriber->__invoke(new UserCreated($this->createStub(Company::class), $user));
    }

    public function testItIsSubscribedToUserCreatedEvent(): void
    {
        $this->subscriber->__invoke(new UserCreated($this->createStub(Company::class), $this->createStub(User::class)));
    }
}
