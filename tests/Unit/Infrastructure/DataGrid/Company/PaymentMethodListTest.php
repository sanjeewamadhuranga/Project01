<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid\Company;

use App\Application\DataGrid\Filters\GridRequest;
use App\Application\DataGrid\Filters\SortDirection;
use App\Application\DataGrid\Filters\Sorting;
use App\Domain\DataGrid\Filters\BasicFilters;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Provider\Provider;
use App\Infrastructure\DataGrid\Company\PaymentMethodList;
use App\Infrastructure\Repository\Company\ProviderOnboardingRepository;
use App\Infrastructure\Repository\ProviderRepository;
use App\Tests\Unit\UnitTestCase;
use Cloudinary\Asset\Image;
use Cloudinary\Cloudinary;

class PaymentMethodListTest extends UnitTestCase
{
    public function testItReturnsProviders(): void
    {
        $title = ['Title 1', 'Title 2'];
        $value = ['Value 1', 'Value 2'];
        $provider1 = new Provider();
        $provider1->setTitle($title[0]);
        $provider1->setValue($value[0]);
        $provider2 = new Provider();
        $provider2->setTitle($title[1]);
        $provider2->setValue($value[1]);

        $providerRepository = $this->createStub(ProviderRepository::class);
        $providerOnboardingRepository = $this->createStub(ProviderOnboardingRepository::class);
        $providerRepository->method('getNotDeleted')->willReturn([$provider1, $provider2]);

        $list = new PaymentMethodList(
            $providerRepository,
            $this->createStub(Cloudinary::class),
            $providerOnboardingRepository
        );
        $items = $list->getData(new GridRequest(new BasicFilters(), new Sorting(null, SortDirection::ASC)))->getItems();

        self::assertSame([$provider1, $provider2], $items);
    }

    public function testItTransformsProviderIntoArray(): void
    {
        $id = '61f021a163b571290822cdd8';
        $title = 'some title';
        $value = 'some value';
        $icon = 'icon.png';
        $logo = 'logo.jpeg';
        $logoUrl = 'some/url/logo.jpeg';
        $description = 'it is description';

        $provider = new Provider();
        $provider->setId($id);
        $provider->setTitle($title);
        $provider->setValue($value);
        $provider->setIcon($icon);
        $provider->setLogo($logo);
        $provider->setDescription($description);

        $company = $this->createStub(Company::class);
        $company->method('getEnabledProviders')->willReturn([$value]);

        $paymentMethodList = new PaymentMethodList(
            $this->createStub(ProviderRepository::class),
            $this->getCloudinary($logoUrl),
            $this->createStub(ProviderOnboardingRepository::class)
        );
        $paymentMethodList->setCompany($company);

        self::assertSame([
            'id' => $id,
            'title' => $title,
            'value' => $value,
            'icon' => $icon,
            'logo' => $logo,
            'logo_url' => $logoUrl,
            'description' => $description,
            'enabled' => true,
            'unavailable' => false,
        ], $paymentMethodList->transform($provider, 0));
    }

    private function getCloudinary(string $imageUrl): Cloudinary
    {
        $image = $this->createStub(Image::class);
        $image->method('toUrl')->willReturn($imageUrl);
        $image->method('resize')->willReturn($image);

        $cloudinary = $this->createStub(Cloudinary::class);
        $cloudinary->method('image')->willReturn($image);

        return $cloudinary;
    }
}
