<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Listener\Security;

use App\Application\Listener\Security\TwoFactorSetupListener;
use App\Domain\ActivityLog\ActivityLogType;
use App\Domain\Document\Log\Log;
use App\Domain\Document\Security\Administrator;
use App\Domain\Event\User\TwoFactorDisableEvent;
use App\Domain\Event\User\TwoFactorSetupEvent;
use App\Infrastructure\Repository\LogRepository;
use App\Tests\Unit\UnitTestCase;

class TwoFactorSetupListenerTest extends UnitTestCase
{
    /**
     * @dataProvider getSetupProvider
     */
    public function testItReturnsSavedLogWithSetupLogTypeWhenProvideSetupLogType(string $activityLogType, string $mfa): void
    {
        $logRepository = $this->createMock(LogRepository::class);
        $user = $this->createStub(Administrator::class);
        $logRepository->expects(self::once())->method('save')->with(self::callback(static fn (Log $log) => $activityLogType === $log->getType()));

        $event = new TwoFactorSetupEvent($user, $mfa);
        (new TwoFactorSetupListener($logRepository))->onTwoFactorSetup($event);
    }

    /**
     * @dataProvider getDisableProvider
     */
    public function testItReturnsSaveLogWithDisableLogTypeWhenProvideDisableLogType(string $activityLogType, string $mfa): void
    {
        $logRepository = $this->createMock(LogRepository::class);
        $user = $this->createStub(Administrator::class);
        $logRepository->expects(self::once())->method('save')->with(self::callback(static fn (Log $log) => $activityLogType === $log->getType()));

        $event = new TwoFactorDisableEvent($user, $mfa);
        (new TwoFactorSetupListener($logRepository))->onTwoFactorDisable($event);
    }

    /**
     * @return iterable<string, array{string,string}>
     */
    public function getSetupProvider(): iterable
    {
        yield 'SMS Setup' => [ActivityLogType::TWO_FACTOR_SETUP_SMS, Administrator::MFA_SMS];
        yield 'Google Setup' => [ActivityLogType::TWO_FACTOR_SETUP_APP, Administrator::MFA_GOOGLE];
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public function getDisableProvider(): iterable
    {
        yield 'SMS Disable' => [ActivityLogType::TWO_FACTOR_DISABLE_SMS, Administrator::MFA_SMS];
        yield 'Google Disable' => [ActivityLogType::TWO_FACTOR_DISABLE_APP, Administrator::MFA_GOOGLE];
    }
}
