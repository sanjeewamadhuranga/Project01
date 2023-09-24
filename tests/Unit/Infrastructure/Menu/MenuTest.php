<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Menu;

use App\Application\Security\Permissions\Action;
use App\Application\Security\Permissions\Permission;
use App\Application\Setup\CompleteSetupDetector;
use App\Domain\Settings\Branding;
use App\Domain\Settings\Config;
use App\Domain\Settings\Features;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Menu\MenuItem;
use App\Infrastructure\Menu\SideMenu;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuTest extends UnitTestCase
{
    private Config $config;

    private SystemSettings&MockObject $systemSettings;

    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    protected function setUp(): void
    {
        $this->systemSettings = $this->createPartialMock(SystemSettings::class, ['getValue']);
        $this->config = new Config(new Features($this->systemSettings), $this->systemSettings, new Branding($this->systemSettings), $this->createStub(CompleteSetupDetector::class));
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        parent::setUp();
    }

    /**
     * @dataProvider permissionProvider
     *
     * @param list<string|null>|string $expectedRoutes
     * @param array<string,mixed>      $settings
     */
    public function testItShowsMenuBaseOnSettingsFeaturesAndPermissions(string $permission, string|array $expectedRoutes, array $settings = []): void
    {
        $this->systemSettings->method('getValue')->willReturnCallback(fn ($key) => $settings[$key] ?? null);
        $menuRoutes = $this->getMenuRoutes($permission);
        self::assertSame(['dashboard', ...(array) $expectedRoutes], $menuRoutes);
    }

    /**
     * @return array<int|string, string|null>
     */
    private function getMenuRoutes(string $permission): array
    {
        $userPermissions = [$permission];

        $this->authorizationChecker->method('isGranted')
            ->willReturnCallback(static fn (string $attribute) => in_array($attribute, $userPermissions, true));

        $menu = new SideMenu($this->config, $this->authorizationChecker);

        return array_map(static fn (MenuItem $item) => (true === $item->isDivider()) ? $item->getLabel() : $item->getRoute(), [...$menu->getItems()]);
    }

    /**
     * @return iterable<array{0: string, 1: list<string|null>|string, 2?: array<string, mixed>}>
     */
    public function permissionProvider(): iterable
    {
        yield 'Merchant menu' => [Permission::MODULE_MERCHANT.Action::ANY, 'merchants'];
        yield 'Transaction menu' => [Permission::MODULE_TRANSACTION.Action::ANY, 'transaction_index'];
        yield 'Remittance Menu' => [Permission::MODULE_REMITTANCE.Action::ANY, 'remittance_index'];
        yield 'Report Menu' => [Permission::MODULE_REPORTS.Action::ANY, 'reports'];
        yield 'Administrators Menu' => [Permission::MODULE_ADMINISTRATORS.Action::ANY, ['menu.config', 'administrators_index']];
        yield 'Configurations Menu' => [Permission::MODULE_CONFIGURATION.Action::ANY, ['menu.config', 'configuration_index']];
        yield 'Compliance Menu' => [Permission::MODULE_COMPLIANCE.Action::ANY, ['menu.config']];
        yield 'Onboarding Menu' => [Permission::MODULE_ONBOARDING.Action::ANY, 'onboarding', [SystemSettings::SHOW_REGISTRATION_PROVISION => 'true']];
        yield 'Onboarding Menu does not show if show registration provision is disabled' => [Permission::MODULE_ONBOARDING.Action::ANY, [], [SystemSettings::SHOW_REGISTRATION_PROVISION => 'false']];
        yield 'Offer Menu' => [Permission::MODULE_OFFER.Action::ANY, 'offer', [SystemSettings::SHOW_OFFERS => 'true']];
        yield 'Offer Menu does not show if setting is disabled' => [Permission::MODULE_OFFER.Action::ANY, [], [SystemSettings::SHOW_OFFERS => 'false']];
        yield 'CurrencyFX Menus' => [Permission::MODULE_CURRENCY_FX.Action::ANY, 'currency_fx', [
            SystemSettings::ENABLED_FEATURES => [Features::CURRENCY_FX_CONFIGURATION],
        ]];
        yield 'CurrencyFX does not show if feature is disabled' => [Permission::MODULE_CURRENCY_FX.Action::ANY, []];
        yield 'Market place Menu' => [Permission::MODULE_MARKETPLACE.Action::ANY, 'marketplace', [
            SystemSettings::ENABLED_FEATURES => [Features::SHOP],
        ]];
        yield 'Market place Menu does not show if feature is disabled' => [Permission::MODULE_MARKETPLACE.Action::ANY, []];
    }
}
