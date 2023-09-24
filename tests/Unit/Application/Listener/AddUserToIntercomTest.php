<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener;

use App\Application\Company\Intercom;
use App\Application\Listener\AddUserToIntercom;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Domain\Event\Company\UserCreated;
use App\Domain\Settings\Features;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AddUserToIntercomTest extends UnitTestCase
{
    private Intercom&MockObject $intercom;

    private Features&MockObject $feature;

    private Company $company;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->intercom = $this->createMock(Intercom::class);
        $this->feature = $this->createMock(Features::class);
        $this->company = $this->createStub(Company::class);
        $this->user = $this->createStub(User::class);
    }

    public function testItPassesCreatedUserToIntercomIfFeatureIsEnabled(): void
    {
        $this->feature->expects(self::once())->method('isIntercomEnabled')->willReturn(true);
        $subscriber = new AddUserToIntercom($this->intercom, $this->feature);
        $this->intercom->expects(self::once())->method('syncUser')->with($this->company, $this->user);

        $subscriber->__invoke(new UserCreated($this->company, $this->user));
    }

    public function testItFailCreatedUserToIntercomIfFeatureIsDisable(): void
    {
        $this->feature->expects(self::once())->method('isIntercomEnabled')->willReturn(false);
        $subscriber = new AddUserToIntercom($this->intercom, $this->feature);
        $this->intercom->expects(self::never())->method('syncUser');

        $subscriber->__invoke(new UserCreated($this->company, $this->user));
    }
}
