<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration;

use App\Domain\Document\Provider\Provider;
use App\Infrastructure\Form\Configuration\ProviderSettingType;
use App\Infrastructure\Repository\ProviderRepository;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class ProviderSettingTypeTest extends TypeTestCase
{
    private ProviderRepository&Stub $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createStub(ProviderRepository::class);

        parent::setUp();
    }

    public function testItGetsChoicesFromProviderRepository(): void
    {
        $provider1Title = 'provider1Title';
        $provider2Title = 'provider2Title';
        $provider3Title = 'provider3Title';

        $provider1Value = 'provider1Value';
        $provider2Value = 'provider2Value';
        $provider3Value = 'provider3Value';

        $provider1 = new Provider();
        $provider2 = new Provider();
        $provider3 = new Provider();

        $provider1->setTitle($provider1Title);
        $provider2->setTitle($provider2Title);
        $provider3->setTitle($provider3Title);

        $provider1->setValue($provider1Value);
        $provider2->setValue($provider2Value);
        $provider3->setValue($provider3Value);

        $this->repository->method('findAll')->willReturn([$provider1, $provider2, $provider3]);

        $form = $this->factory->create(ProviderSettingType::class);

        self::assertSame([
            $provider1Title.' ('.$provider1Value.')' => $provider1Value,
            $provider2Title.' ('.$provider2Value.')' => $provider2Value,
            $provider3Title.' ('.$provider3Value.')' => $provider3Value,
        ], $form->getConfig()->getOptions()['choices']);
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new ProviderSettingType($this->repository)], []),
        ];
    }
}
