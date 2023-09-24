<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Company;

use App\Domain\Document\App;
use App\Infrastructure\DataGrid\Company\AppsList;
use App\Infrastructure\Repository\AppRepository;
use App\Tests\Unit\UnitTestCase;

class AppsListTest extends UnitTestCase
{
    public function testItTransformsAppIntoArray(): void
    {
        $id = '61f0213b6c0d85172231b509';
        $name = 'some name';
        $appId = 'appID';
        $domain = 'domain';
        $appModel = 'appModel';
        $currency = 'USD';
        $appType = 'appType';

        $app = $this->createStub(App::class);
        $app->method('getId')->willReturn($id);
        $app->method('getName')->willReturn($name);
        $app->method('getAppId')->willReturn($appId);
        $app->method('getDomain')->willReturn($domain);
        $app->method('getAppModel')->willReturn($appModel);
        $app->method('getCurrency')->willReturn($currency);
        $app->method('getAppType')->willReturn($appType);

        $appList = new AppsList($this->createStub(AppRepository::class));

        self::assertSame([
            'id' => $id,
            'name' => $name,
            'appId' => $appId,
            'domain' => $domain,
            'appModel' => $appModel,
            'currency' => $currency,
            'type' => $appType,
        ], $appList->transform($app, 0));
    }
}
