<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration\Setting;

use App\Domain\Settings\SettingsInterface;
use App\Infrastructure\Form\Configuration\Settings\FederatedIdentitySetupType;
use App\Infrastructure\Form\DataTransformer\StringToBooleanDataTransformer;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\HttpFoundation\ServerBag;

class FederatedIdentitySetupTypeTest extends BaseSystemSettingTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->request->server = new ServerBag(['REMOTE_ADDR' => 'test']);
    }

    public function testItAddsStringToBooleanTransformer(): void
    {
        $form = $this->factory->create(FederatedIdentitySetupType::class);

        self::assertInstanceOf(StringToBooleanDataTransformer::class, $form->get(SettingsInterface::FEDERATED_ID_PASSWORDLESS_LOGIN)->getConfig()->getModelTransformers()[0]);
    }

    /**
     * @return array<int, PreloadedExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new FederatedIdentitySetupType($this->settings, $this->requestStack)], []),
        ];
    }
}
