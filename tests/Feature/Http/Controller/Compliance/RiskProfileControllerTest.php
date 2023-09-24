<?php

declare(strict_types=1);

namespace App\Tests\Feature\Http\Controller\Compliance;

use App\Domain\Document\Compliance\RiskProfile;
use App\Infrastructure\Repository\RiskProfileRepository;
use App\Tests\Feature\BaseTestCase;

class RiskProfileControllerTest extends BaseTestCase
{
    /**
     * @group smoke
     */
    public function testItShowsRiskProfileList(): void
    {
        self::$client->request('GET', '/compliance/risk-profiles');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('risk-profile-list');
    }

    /**
     * @group smoke
     */
    public function testItListsRiskProfile(): void
    {
        self::$client->request('GET', '/compliance/risk-profiles/list');

        $this->assertGridResponse();
    }

    /**
     * @group smoke
     */
    public function testItShowsRiskProfileDetails(): void
    {
        /** @var RiskProfile $riskProfile */
        $riskProfile = self::$fixtures['risk_profile_test'];
        self::$client->request('GET', sprintf('/compliance/risk-profiles/%s', $riskProfile->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'test');
    }

    public function testItAllowsToDeleteARiskProfile(): void
    {
        /** @var RiskProfile $riskProfile */
        $riskProfile = self::$fixtures['risk_profile_test'];
        self::$client->request('DELETE', sprintf('/compliance/risk-profiles/%s/delete', $riskProfile->getId()));

        self::assertResponseIsSuccessful();
        $testProfile = $this->getDocumentManager()->find(RiskProfile::class, $riskProfile->getId());
        self::assertInstanceOf(RiskProfile::class, $testProfile);
        self::assertTrue($testProfile->isDeleted());
    }

    public function testItCreatesRiskProfile(): void
    {
        self::$client->request('GET', '/compliance/risk-profiles/create');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Create Risk profile');

        self::$client->submitForm('Submit', [
            'risk_profile' => [
                'code' => 'risk profile code',
                'currency' => 'EUR',
            ],
        ]);

        $riskProfile = self::getContainer()->get(RiskProfileRepository::class)->findOneBy(['code' => 'risk profile code']);
        self::assertInstanceOf(RiskProfile::class, $riskProfile);
        self::assertSame('EUR', $riskProfile->getCurrency());
    }

    public function testItUpdateRiskProfile(): void
    {
        /** @var RiskProfile $riskProfile */
        $riskProfile = self::$fixtures['risk_profile_test'];

        self::$client->request('GET', sprintf('/compliance/risk-profiles/%s/edit', $riskProfile->getId()));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('html', 'Update Risk profile');

        self::$client->submitForm('Submit', [
            'risk_profile' => [
                'code' => 'code update',
            ],
        ]);

        $this->getDocumentManager()->persist($riskProfile);
        $this->getDocumentManager()->refresh($riskProfile);
        self::assertSame('code update', $riskProfile->getCode());
    }
}
