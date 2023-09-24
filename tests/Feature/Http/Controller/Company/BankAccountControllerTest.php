<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Domain\Document\Company\BankAccount;
use App\Tests\Feature\BaseTestCase;

class BankAccountControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsBankAccountListPage(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/bank-accounts', $this->getTestCompany()->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('company-bank-account-list');
    }

    /**
     * @group smoke
     */
    public function testItListsBankAccounts(): void
    {
        self::$client->request('GET', sprintf('/merchants/%s/bank-accounts/list', $this->getTestCompany()->getId()));

        $this->assertGridResponse();
    }

    public function testItDeletesBankAccount(): void
    {
        $this->markTouchesDb();
        $company = $this->refresh($this->getTestCompany());
        $company->getBankAccounts()->add(new BankAccount());
        $this->getDocumentManager()->persist($company);
        $this->getDocumentManager()->flush();

        self::$client->request('DELETE', sprintf('/merchants/%s/bank-accounts/0/delete', $this->getTestCompany()->getId()));

        self::assertResponseIsSuccessful();

        self::assertCount(0, $this->refresh($company)->getBankAccounts());
    }
}
