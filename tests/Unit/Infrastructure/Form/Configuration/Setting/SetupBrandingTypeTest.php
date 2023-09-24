<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration\Setting;

use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Configuration\Settings\SetupBrandingType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\HttpFoundation\ServerBag;

class SetupBrandingTypeTest extends BaseSystemSettingTest
{
    /**
     * @dataProvider removeAddressDataProvider
     */
    public function testItHasCorrectDefaultValues(string $remoteAddress, string $clientDomain): void
    {
        $this->request->server = new ServerBag(['REMOTE_ADDR' => $remoteAddress]);

        $form = $this->factory->create(SetupBrandingType::class);

        self::assertSame('https://dashboard.'.$clientDomain, $form->get(SystemSettings::DASHBOARD)->getData());
        self::assertSame('https://api.'.$clientDomain, $form->get(SystemSettings::API_DOMAIN)->getData());
        self::assertSame('https://.'.$clientDomain, $form->get(SystemSettings::MANAGER_PORTAL_URL)->getData());
    }

    /**
     * @return iterable<string, string[]>
     */
    public function removeAddressDataProvider(): iterable
    {
        yield 'simple domain' => ['test', 'test'];
        yield 'domain with manager' => ['manager.pay', 'pay'];
        yield 'domain with ' => ['.someDomain', 'someDomain'];
    }

    /**
     * @return array<int, PreloadedExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new SetupBrandingType($this->settings, $this->requestStack)], []),
        ];
    }
}
