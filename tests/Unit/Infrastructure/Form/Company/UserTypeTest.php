<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Company;

use App\Application\Validation\NotInArray;
use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Domain\Settings\Branding;
use App\Domain\Settings\Config;
use App\Domain\Settings\Features;
use App\Domain\Settings\FederatedIdentityType;
use App\Domain\Settings\SystemSettings;
use App\Domain\Settings\Theme;
use App\Infrastructure\Form\Company\Address\AddressType;
use App\Infrastructure\Form\Company\Address\StateType;
use App\Infrastructure\Form\Company\UserType;
use App\Infrastructure\Repository\RoleRepository;
use App\Tests\Unit\Infrastructure\Form\TypeTestCaseWithManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use MongoDB\Collection;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Validator\Validation;

class UserTypeTest extends TypeTestCaseWithManagerRegistry
{
    private Config&Stub $config;

    private SystemSettings&Stub $settings;

    private Features&Stub $features;

    private Company&Stub $company;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);

        $this->features = $this->createStub(Features::class);

        $this->config = $this->createStub(Config::class);
        $this->config->method('getSettings')->willReturn($this->settings);
        $this->config->method('getFeatures')->willReturn($this->features);
        $this->config->method('getBranding')->willReturn(new Branding($this->settings));

        $this->company = $this->createStub(Company::class);
        $this->company->method('getUsers')->willReturn(new ArrayCollection([]));

        parent::setUp();
    }

    public function testItDisablesContactEmailWhenFederatedTypeIdentityIsEmail(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::EMAIL);
        $this->features->method('isKycEnabled')->willReturn(false);

        $form = $this->factory->create(UserType::class, options: ['company' => $this->company]);

        self::assertTrue($form->get('contactEmail')->getConfig()->getOptions()['disabled']);
        self::assertFalse($form->get('mobile')->getConfig()->getOptions()['disabled']);
    }

    public function testItDisablesMobileWhenFederatedTypeIdentityIsPhoneNumber(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);
        $this->features->method('isKycEnabled')->willReturn(false);

        $form = $this->factory->create(UserType::class, options: ['company' => $this->company]);

        self::assertFalse($form->get('contactEmail')->getConfig()->getOptions()['disabled']);
        self::assertTrue($form->get('mobile')->getConfig()->getOptions()['disabled']);
    }

    public function testThereIsNoKycFieldsWhenKycIsDisabled(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);
        $this->features->method('isKycEnabled')->willReturn(false);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Child "requireKyc" does not exist.');

        $form = $this->factory->create(UserType::class, options: ['company' => $this->company]);
        $form->get('requireKyc');
    }

    public function testItAddsKycFieldsWhenKycIsEnabled(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);
        $this->features->method('isKycEnabled')->willReturn(true);

        $form = $this->factory->create(UserType::class, options: ['company' => $this->company]);

        self::assertInstanceOf(Form::class, $form->get('requireKyc'));
        self::assertInstanceOf(Form::class, $form->get('dob'));
        self::assertInstanceOf(Form::class, $form->get('addresses'));
    }

    public function testItDisablesContactEmailWhenCorrespondedOptionProvided(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);
        $this->features->method('isKycEnabled')->willReturn(false);

        $form = $this->factory->create(UserType::class, options: ['company' => $this->company, 'disable_email' => true]);

        self::assertTrue($form->get('contactEmail')->getConfig()->getOptions()['disabled']);
    }

    public function testItDisablesMobileWhenCorrespondedOptionProvided(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::EMAIL);
        $this->features->method('isKycEnabled')->willReturn(false);

        $form = $this->factory->create(UserType::class, options: ['company' => $this->company, 'disable_mobile' => true]);

        self::assertTrue($form->get('mobile')->getConfig()->getOptions()['disabled']);
    }

    public function testContactEmailFieldContainsConstraintWithAlreadyUsedEmailAddresses(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);
        $this->features->method('isKycEnabled')->willReturn(false);

        $localCompany = $this->createStub(Company::class);
        $localCompany->method('getUsers')->willReturn(new ArrayCollection([
            $this->getUser('aaa@pay.com'),
            $this->getUser('bbb@pay.com'),
            $this->getUser('ccc@pay.com'),
        ]));

        $form = $this->factory->create(UserType::class, options: ['company' => $localCompany]);

        $constraints = $form->get('contactEmail')->getConfig()->getOption('constraints');
        self::assertCount(1, $constraints);
        $constraint = $constraints[0];
        self::assertInstanceOf(NotInArray::class, $constraint);
        self::assertSame([
            'aaa@pay.com',
            'bbb@pay.com',
            'ccc@pay.com',
        ], $constraint->choices);
    }

    public function testMobileFieldContainsConstraintWithAlreadyUsedPhoneNumbers(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::EMAIL);
        $this->features->method('isKycEnabled')->willReturn(false);

        $localCompany = $this->createStub(Company::class);
        $localCompany->method('getUsers')->willReturn(new ArrayCollection([
            $this->getUser(mobile: '+44 700 800 900'),
            $this->getUser(mobile: '+45 777 888 999'),
            $this->getUser(mobile: '+46 123 456 789'),
        ]));

        $form = $this->factory->create(UserType::class, options: ['company' => $localCompany]);

        $constraints = $form->get('mobile')->getConfig()->getOption('constraints');
        self::assertCount(1, $constraints);
        $constraint = $constraints[0];
        self::assertInstanceOf(NotInArray::class, $constraint);
        self::assertSame([
            '+44 700 800 900',
            '+45 777 888 999',
            '+46 123 456 789',
        ], $constraint->choices);
    }

    public function testNationalIdentityIsRenderedWhenAdminThemeIsDialog(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);
        $this->features->method('isKycEnabled')->willReturn(false);
        $this->settings->method('getValue')->willReturn(Theme::DIALOG);

        $form = $this->factory->create(UserType::class, options: ['company' => $this->company, 'disable_email' => true]);

        self::assertInstanceOf(Form::class, $form->get('nationalIdentity'));
    }

    public function testNotionalIdentityIsNotRenderedWhenThemIsNotDialog(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);
        $this->features->method('isKycEnabled')->willReturn(false);
        $this->settings->method('getValue')->willReturn(Theme::PAY);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Child "nationalIdentity" does not exist.');

        $form = $this->factory->create(UserType::class, options: ['company' => $this->company, 'disable_email' => true]);
        $form->get('nationalIdentity');
    }

    private function getUser(string $email = null, string $mobile = null): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setMobile($mobile);

        return $user;
    }

    /**
     * @return array<int, PreloadedExtension|ValidatorExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new UserType($this->config)], []),
            new PreloadedExtension([new DocumentType($this->getManagerRegistry())], []),
            new ValidatorExtension(Validation::createValidator()),
            new PreloadedExtension([new AddressType(new Branding($this->settings))], []),
            new PreloadedExtension([new StateType(new Branding($this->settings))], []),
        ];
    }

    protected function getRepository(array $items = []): RoleRepository // @phpstan-ignore-line
    {
        $collection = $this->createStub(Collection::class);
        $collection->method('find')->willReturn(new ArrayCollection($items));

        $query = new Query(
            $this->createStub(DocumentManager::class),
            $this->createStub(ClassMetadata::class),
            $collection,
            [
                'type' => Query::TYPE_FIND,
                'query' => [],
            ],
            hydrate: false,
        );
        $builder = $this->createStub(Builder::class);
        $builder->method('getQuery')->willReturn($query);

        $repository = $this->createMock(RoleRepository::class);
        $repository->expects(self::once())->method('forCompanyQuery')->willReturn($builder);

        return $repository;
    }
}
