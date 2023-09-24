<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Company;

use App\Domain\Document\App;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Form\Company\AppType;
use App\Infrastructure\Repository\AppRepository;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class AppTypeTest extends TypeTestCase
{
    private AppRepository&MockObject $appRepository;

    protected function setUp(): void
    {
        $this->appRepository = $this->createMock(AppRepository::class);

        parent::setUp();
    }

    public function testItThrowsExceptionWhenNoCompanyProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->create(AppType::class);
    }

    public function testItThrowsExceptionWhenWrongCompanyProvided(): void
    {
        $company = new Administrator();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Wrong company provided, expected %s, provided %s', Company::class, $company::class));

        $this->factory->create(AppType::class, options: ['company' => $company]);
    }

    public function testItGetsChoicesFromAppRepository(): void
    {
        $companyId = '5beb13f49006e85e1dacd100';
        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn($companyId);

        $app1Id = 'appId1';
        $app2Id = 'appId2';
        $app3Id = 'appId3';

        $app1Name = 'appName1';
        $app2Name = 'appName2';
        $app3Name = 'appName3';

        $app1ResellerId = 'appResellerId1';
        $app2ResellerId = 'appResellerId2';
        $app3ResellerId = 'appResellerId3';

        $this->appRepository->method('getForCompany')->with($company)->willReturn([
            $this->createApp($app1Id, $app1Name, $app1ResellerId),
            $this->createApp($app2Id, $app2Name, $app2ResellerId),
            $this->createApp($app3Id, $app3Name, $app3ResellerId),
        ]);

        $form = $this->factory->create(AppType::class, options: ['company' => $company]);

        /** @var CallbackChoiceLoader $callbackChoiceLoader */
        $callbackChoiceLoader = $form->getConfig()->getOptions()['choice_loader'];

        self::assertSame([
            $app1Id => $app1Name.' '.$app1ResellerId,
            $app2Id => $app2Name.' '.$app2ResellerId,
            $app3Id => $app3Name.' '.$app3ResellerId,
        ], $callbackChoiceLoader->loadChoiceList()->getOriginalKeys());
    }

    private function createApp(string $id, string $name, string $resellerId): App
    {
        $app = new App();
        $app->setAppId($id);
        $app->setName($name);
        $app->setResellerId($resellerId);

        return $app;
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new AppType($this->appRepository)], []),
        ];
    }
}
