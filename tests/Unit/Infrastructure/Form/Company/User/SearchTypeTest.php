<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Company\User;

use App\Application\Validation\Company\PhoneNumber;
use App\Domain\Settings\FederatedIdentityType;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Company\User\SearchType;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Validation;

class SearchTypeTest extends TypeTestCase
{
    private SystemSettings&Stub $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createStub(SystemSettings::class);

        parent::setUp();
    }

    public function testConstraintsAreNotBlankUuidAndEmailWhenFederatedIdentityIsEmail(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::EMAIL);

        $form = $this->factory->create(SearchType::class);
        $options = $form->get('identifier')->getConfig()->getOptions();
        $constraints = $options['constraints'];

        self::assertCount(2, $constraints);
        self::assertInstanceOf(NotBlank::class, $constraints[0]);
        self::assertInstanceOf(AtLeastOneOf::class, $constraints[1]);

        self::assertCount(2, $constraints[1]->constraints);
        self::assertInstanceOf(Uuid::class, $constraints[1]->constraints[0]);
        self::assertInstanceOf(Email::class, $constraints[1]->constraints[1]);
    }

    public function testConstraintsAreNotBlankUuidAndPhoneNumberWhenFederatedIdentityIsPhoneNumber(): void
    {
        $this->settings->method('getFederatedIdentityType')->willReturn(FederatedIdentityType::PHONE_NUMBER);

        $form = $this->factory->create(SearchType::class);
        $options = $form->get('identifier')->getConfig()->getOptions();
        $constraints = $options['constraints'];

        self::assertCount(2, $constraints);
        self::assertInstanceOf(NotBlank::class, $constraints[0]);
        self::assertInstanceOf(AtLeastOneOf::class, $constraints[1]);

        self::assertCount(2, $constraints[1]->constraints);
        self::assertInstanceOf(Uuid::class, $constraints[1]->constraints[0]);
        self::assertInstanceOf(PhoneNumber::class, $constraints[1]->constraints[1]);
    }

    /**
     * @return array<int, PreloadedExtension|ValidatorExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new SearchType($this->settings)], []),
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
