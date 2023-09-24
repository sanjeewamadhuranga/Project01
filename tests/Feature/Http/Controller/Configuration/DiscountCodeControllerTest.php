<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Configuration;

use App\Domain\Document\DiscountCode;
use App\Infrastructure\Repository\DiscountCodeRepository;
use App\Tests\Feature\BaseTestCase;

class DiscountCodeControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsDiscountCodeList(): void
    {
        self::$client->request('GET', '/configuration/discount-codes');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('discount-code-list');
    }

    /**
     * @group smoke
     */
    public function testItListsDiscountCode(): void
    {
        self::$client->request('GET', '/configuration/discount-codes/list');

        $this->assertGridResponse();
    }

    /**
     * @group smoke
     */
    public function testItShowsDiscountCodeDetails(): void
    {
        /** @var DiscountCode $testDiscountCode */
        $testDiscountCode = self::$fixtures['discount_code_test'];
        self::$client->request('GET', sprintf('/configuration/discount-codes/%s', $testDiscountCode->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'test');
    }

    public function testItAllowsToDeleteADiscountCode(): void
    {
        /** @var DiscountCode $testDiscountCode */
        $testDiscountCode = self::$fixtures['discount_code_test'];
        self::$client->request('DELETE', sprintf('/configuration/discount-codes/%s/delete', $testDiscountCode->getId()));

        self::assertResponseIsSuccessful();
        $testDiscountCode = $this->getDocumentManager()->find(DiscountCode::class, $testDiscountCode->getId());
        self::assertInstanceOf(DiscountCode::class, $testDiscountCode);
        self::assertTrue($testDiscountCode->isDeleted());
    }

    public function testItCreatesDiscountCode(): void
    {
        self::$client->request('GET', '/configuration/discount-codes/create');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Create Discount code');

        self::$client->submitForm('Submit', [
            'discount_code' => [
                'title' => 'test',
                'description' => 'test',
                'code' => 'test',
            ],
        ]);

        $discountCode = self::getContainer()->get(DiscountCodeRepository::class)->findOneBy(['code' => 'test']);
        self::assertInstanceOf(DiscountCode::class, $discountCode);
    }

    public function testItUpdateDiscountCode(): void
    {
        /** @var DiscountCode $testDiscountCode */
        $testDiscountCode = self::$fixtures['discount_code_test'];

        self::$client->request('GET', sprintf('/configuration/discount-codes/%s/edit', $testDiscountCode->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Update Discount code');

        self::$client->submitForm('Submit', [
            'discount_code' => [
                'code' => 'update',
            ],
        ]);

        $this->getDocumentManager()->persist($testDiscountCode);
        $this->getDocumentManager()->refresh($testDiscountCode);
        self::assertSame('update', $testDiscountCode->getCode());
    }
}
