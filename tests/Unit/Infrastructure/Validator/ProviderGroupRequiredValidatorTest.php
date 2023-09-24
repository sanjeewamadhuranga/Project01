<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Validator;

use App\Domain\Document\Provider\Provider;
use App\Infrastructure\Repository\ProviderRepository;
use App\Infrastructure\Validator\ProviderGroupRequired;
use App\Infrastructure\Validator\ProviderGroupRequiredValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<ProviderGroupRequiredValidator>
 */
class ProviderGroupRequiredValidatorTest extends ConstraintValidatorTestCase
{
    private readonly ProviderRepository&MockObject $providerRepository;

    protected function setUp(): void
    {
        $this->providerRepository = $this->createMock(ProviderRepository::class);

        parent::setUp();
    }

    public function testItDoesNotRequireGroupWhenNoOtherProviderWithSameNameExists(): void
    {
        $value = 'test-provider';
        $provider = $this->getProvider($value);
        $this->expectRepositoryToFindProvidersWithSameValue($value, [$provider]);

        $this->validator->validate($provider, new ProviderGroupRequired());

        $this->assertNoViolation();
    }

    public function testItRaisesViolationWhenOtherProviderWithSameNameExistsAndGroupIsNull(): void
    {
        $value = 'test-provider';
        $provider = $this->getProvider($value);
        $providers = [$provider, $this->getProvider($value)];
        $this->expectRepositoryToFindProvidersWithSameValue($value, $providers);

        $this->validator->validate($provider, new ProviderGroupRequired());

        $this->buildViolation('This value should not be blank.')->atPath('property.path.group')->assertRaised();
    }

    public function testItRaisesViolationWhenOtherProviderWithSameNameExistsAndGroupIsEmpty(): void
    {
        $value = 'test-provider';
        $provider = $this->getProvider($value, '');
        $providers = [$provider, $this->getProvider($value)];
        $this->expectRepositoryToFindProvidersWithSameValue($value, $providers);

        $this->validator->validate($provider, new ProviderGroupRequired());

        $this->buildViolation('This value should not be blank.')->atPath('property.path.group')->assertRaised();
    }

    public function testItDoesNotRaiseViolationWhenGroupIsProvided(): void
    {
        $provider = $this->getProvider('test-provider', 'test-group');

        $this->validator->validate($provider, new ProviderGroupRequired());

        $this->assertNoViolation();
    }

    protected function createValidator(): ProviderGroupRequiredValidator
    {
        return new ProviderGroupRequiredValidator($this->providerRepository);
    }

    /**
     * @param Provider[] $providers
     */
    private function expectRepositoryToFindProvidersWithSameValue(string $value, array $providers): void
    {
        $this->providerRepository
            ->expects(self::once())
            ->method('findByValue')
            ->with($value)
            ->willReturn($providers);
    }

    protected function getProvider(string $value, ?string $group = null): Provider
    {
        $provider = new Provider();
        $provider->setValue($value);
        $provider->setGroup($group);

        return $provider;
    }
}
