<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Company;

use App\Domain\Company\ReviewStatus;
use App\Domain\Company\RiskLevel;
use App\Domain\Document\App;
use App\Domain\Document\Company\ResellerMetadata;
use App\Domain\Document\Location\Location;
use App\Tests\Feature\BaseTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Happyr\ServiceMocking\ServiceMock;
use Knp\Snappy\Pdf;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class CompanyControllerTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTouchesDb();
    }

    /**
     * @group smoke
     */
    public function testItShowsCompanyOverviewAndDetails(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s', $testCompany->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Test company');

        self::$client->request('GET', sprintf('/merchants/%s/details', $testCompany->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Test company');
    }

    /**
     * @group smoke
     */
    public function testItListsCompanies(): void
    {
        self::$client->request('GET', '/merchants');
        self::assertResponseIsSuccessful();
        self::$client->request('GET', '/merchants/list');
        self::assertResponseIsSuccessful();
        $this->assertGridResponse();
    }

    public function testEditCompanyInvalid(): void
    {
        $testCompany = $this->getTestCompany();

        self::$client->request('GET', sprintf('/merchants/%s/edit', $testCompany->getId()));
        self::$client->followRedirects();
        self::assertResponseIsSuccessful();
        self::$client->submitForm('Submit', [
            'company_edit' => [
                'businessEmail' => 'test',
            ],
        ]);

        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('.invalid-feedback', 'email', 'Form should include invalid message');
        self::assertNotSame('test', $testCompany->getBusinessEmail(), 'Email should not have changed');
    }

    public function testEditCompanyFormValid(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s/edit', $testCompany->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Edit company details');
        self::$client->submitForm('Submit', [
            'company_edit' => [
                'tradingName' => ' Pay',
                'registeredName' => ' Pay Pte Ltd',
                'phone' => '+65123456789',
                'companyLegalType' => 'PLC',
                'companyNumber' => 'A123B',
                'businessEmail' => 'test@test.com',
                'businessWebsite' => 'https://www.pay.com',
                'language' => 'EN',
                'timezone' => 'Europe/London',
                'country' => 'SG',
            ],
        ]);

        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);
        self::assertSame('+65123456789', $testCompany->getPhone());
        self::assertSame(' Pay', $testCompany->getTradingName());
        self::assertSame(' Pay Pte Ltd', $testCompany->getRegisteredName());
        self::assertSame('PLC', $testCompany->getCompanyLegalType());
        self::assertSame('A123B', $testCompany->getCompanyNumber());
        self::assertSame('test@test.com', $testCompany->getBusinessEmail());
        self::assertSame('https://www.pay.com', $testCompany->getBusinessWebsite());
        self::assertSame('EN', $testCompany->getLanguage());
        self::assertSame('Europe/London', $testCompany->getTimezone());
        self::$client->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testEditCompanyFinancialFormValid(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s/edit-financial', $testCompany->getId()));
        self::assertSelectorTextContains('html', 'Edit Financial details');
        self::assertSame('GBP', $testCompany->getCurrency());

        self::$client->submitForm('Submit', [
            'financial_edit' => [
                'reviewStatus' => ReviewStatus::VERIFIED->value,
                'riskLevel' => RiskLevel::LOW->value,
                'reviewReference' => 'Test Reference',
                'currency' => 'USD',
                'resellerMetadata' => [
                    'holdCode' => 'Test Hold Code',
                ],
            ],
        ]);

        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);

        $resellerMetaData = $testCompany->getResellerMetadata();
        self::assertSame(RiskLevel::LOW, $testCompany->getRiskLevel());
        self::assertSame(ReviewStatus::VERIFIED, $testCompany->getReviewStatus());
        self::assertSame('Test Reference', $testCompany->getReviewReference());
        self::assertSame('USD', $testCompany->getCurrency());
        self::assertInstanceOf(ResellerMetadata::class, $resellerMetaData);
        self::assertSame('Test Hold Code', $resellerMetaData->getHoldCode());
    }

    public function testEditGoogleAPIAddress(): void
    {
        $this->checkCompanyDetailsPage();
        self::$client->submitForm('Update address', [
            'google_places_address' => [
                'googlePlacesAddress' => 'Singapore 123456',
            ],
        ]);

        $testCompany = $this->getTestCompany();
        $this->getDocumentManager()->persist($testCompany);
        $this->getDocumentManager()->refresh($testCompany);

        self::assertSame('Singapore 123456', $testCompany->getGooglePlacesAddress());
    }

    public function testItAllowsToDeleteCompany(): void
    {
        $this->markTouchesDb();
        $testCompany = $this->refresh($this->getTestCompany());

        $app = new App();
        $testCompany->setApps(new ArrayCollection([$app]));
        $this->getDocumentManager()->persist($app);

        $location = new Location();
        $testCompany->setLocations(new ArrayCollection([$location]));
        $this->getDocumentManager()->persist($location);

        $this->getDocumentManager()->flush();

        self::$client->request('DELETE', sprintf('/merchants/%s/delete', $testCompany->getId()));

        self::assertResponseIsSuccessful();

        $this->getDocumentManager()->refresh($testCompany);
        self::assertTrue($testCompany->isDeleted());

        $this->getDocumentManager()->refresh($app);
        self::assertTrue($app->isDeleted());

        $this->getDocumentManager()->refresh($location);
        self::assertTrue($location->isDeleted());
    }

    public function testItExportsCompanyDataInHtmlFormat(): void
    {
        $testCompany = $this->getTestCompany();
        $crawler = self::$client->request('GET', sprintf('/merchants/%s/details-export/html', $testCompany->getId()));
        self::assertResponseIsSuccessful();
        self::assertSame($testCompany->getRegisteredName(), $crawler->filter('div.pdf-header')->text());
        self::assertGreaterThan(0, $crawler->filter('table')->count());
    }

    public function testItExportsCompanyDataInPdfFormat(): void
    {
        $testCompany = $this->getTestCompany();
        $pdf = $this->createMock(Pdf::class);
        $pdf->expects(self::once())
            ->method('getOutputFromHtml')
            ->with(self::callback(static function (string $html) use ($testCompany): bool {
                $crawler = new Crawler($html);
                self::assertSame($testCompany->getRegisteredName(), $crawler->filter('div.pdf-header')->text());
                self::assertGreaterThan(0, $crawler->filter('table')->count());

                return true;
            }))
            ->willReturn('');
        ServiceMock::swap(self::$client->getContainer()->get(Pdf::class), $pdf);

        self::$client->request('GET', sprintf('/merchants/%s/details-export', $testCompany->getId()));
        self::assertResponseIsSuccessful();

        self::assertMatchesRegularExpression(
            '/^attachment; filename=-Test-company-LTD-\d{14}.pdf$/',
            (string) self::$client->getResponse()->headers->get('Content-Disposition')
        );
    }

    public function testItEditCompanyMetaData(): void
    {
        $testCompany = $this->getTestCompany();
        $crawler = self::$client->request('GET', sprintf('/merchants/%s/details', $testCompany->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Company details');
        $link = $crawler->filter('a.edit-metadata')->eq(0)->link();
        self::$client->click($link);
        self::$client->followRedirects();
        self::assertRouteSame('merchants_edit_metadata');
        self::assertSelectorTextContains('html', 'Edit metadata');

        self::$client->submitForm('Submit', [
           'company_metadata' => [
               'metadata' => [
                   'connectedBusiness' => 'Test connected business',
                   'subsidiariesAffiliates' => 'Test Subsidiaries Affiliates',
                   'businessRegistrationDate' => 'Test Business Registration Date',
                   'income' => [
                       'incomeSource' => 'Contract',
                       'expectedTurnOver' => 'Less than 250,000',
                       'avgTransaction' => 'Test Ave transaction',
                       'maxTransaction' => 'Test max transaction',
                   ],
               ],
           ],
       ]);

        $testCompany = $this->refresh($testCompany);
        $metaData = $testCompany->getMetadata();
        self::assertNotNull($metaData);
        self::assertSame('Test connected business', $metaData->getConnectedBusiness());
        self::assertSame('Test Subsidiaries Affiliates', $metaData->getSubsidiariesAffiliates());
        self::assertSame('Test Business Registration Date', $metaData->getBusinessRegistrationDate());
        self::assertSame('Contract', $metaData->getIncome()?->getIncomeSource());
        self::assertSame('Less than 250,000', $metaData->getIncome()->getExpectedTurnOver());
        self::assertSame('Test Ave transaction', $metaData->getIncome()->getAvgTransaction());
        self::assertSame('Test max transaction', $metaData->getIncome()->getMaxTransaction());
    }

    private function checkCompanyDetailsPage(): void
    {
        $testCompany = $this->getTestCompany();
        self::$client->request('GET', sprintf('/merchants/%s/details', $testCompany->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Company details');
    }
}
