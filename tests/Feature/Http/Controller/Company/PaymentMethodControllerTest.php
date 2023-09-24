<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Tests\Feature\BaseTestCase;

class PaymentMethodControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsPaymentMethodsPage(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/payment-methods', $this->getTestCompany()->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('company-payment-methods-list');
    }

    /**
     * @group smoke
     */
    public function testItListsPaymentMethods(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/payment-methods/list', $this->getTestCompany()->getId()));

        $this->assertGridResponse();
    }
}
