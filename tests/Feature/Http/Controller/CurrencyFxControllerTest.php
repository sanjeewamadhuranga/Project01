<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller;

use App\Domain\Document\CurrencyFx;
use App\Infrastructure\Repository\CurrencyFxRepository;
use App\Tests\Feature\BaseTestCase;

class CurrencyFxControllerTest extends BaseTestCase
{
    public function testItCreatesCurrencyPair(): void
    {
        self::$client->request('GET', '/currency-fx/rates/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'currency_fx' => [
                'currency' => 'EUR',
                'counterCurrency' => 'USD',
                'marketRate' => '1.525525',
                'reference' => '1.525525',
            ],
        ]);
        self::assertResponseRedirects('/currency-fx/rates');

        $currencyFx = self::getContainer()->get(CurrencyFxRepository::class)->findOneBy(['currencyPair' => 'EURUSD']);
        self::assertInstanceOf(CurrencyFx::class, $currencyFx);
    }

    public function testItPreventsUseTheSameCurrencies(): void
    {
        self::$client->request('GET', '/currency-fx/rates/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'currency_fx' => [
                'currency' => 'EUR',
                'counterCurrency' => 'EUR',
                'marketRate' => '1.525525',
                'reference' => '1.525525',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('html', 'This value should not be equal to "EUR"');
    }

    public function testItStoresOnlyUniquePairs(): void
    {
        $currencyFx = new CurrencyFx();
        $currencyFx->setCurrency('EUR');
        $currencyFx->setCounterCurrency('GBP');

        $this->getDocumentManager()->persist($currencyFx);
        $this->getDocumentManager()->flush();

        self::$client->request('GET', '/currency-fx/rates/create');
        self::assertResponseIsSuccessful();

        self::$client->submitForm('Submit', [
            'currency_fx' => [
                'currency' => 'EUR',
                'counterCurrency' => 'GBP',
                'marketRate' => '1.525525',
                'reference' => '1.525525',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('html', 'This value is already used.');
    }
}
