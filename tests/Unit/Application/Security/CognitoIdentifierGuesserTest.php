<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Security;

use App\Application\Security\CognitoIdentifierGuesser;
use App\Domain\Settings\FederatedIdentityType;
use App\Domain\Settings\SystemSettings;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CognitoIdentifierGuesserTest extends UnitTestCase
{
    private ValidatorInterface&MockObject $validator;

    private SystemSettings&MockObject $settings;

    private CognitoIdentifierGuesser $guesser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->settings = $this->createMock(SystemSettings::class);
        $this->guesser = new CognitoIdentifierGuesser($this->validator, $this->settings);
    }

    public function testItResolvesSubIfUuidProvided(): void
    {
        $this->validator->expects(self::once())
            ->method('validate')
            ->with('384abb2b-0f40-4660-a17f-36a75bc17308', [new Uuid()])
            ->willReturn(new ConstraintViolationList());

        self::assertSame('sub', $this->guesser->getIdentifierType('384abb2b-0f40-4660-a17f-36a75bc17308'));
    }

    public function testItResolvesEmailIfInvalidUidIsProvidedAndEmailIsTheIdentifier(): void
    {
        $this->settings->expects(self::once())
            ->method('getFederatedIdentityType')
            ->willReturn(FederatedIdentityType::EMAIL);

        $this->validator->expects(self::once())
            ->method('validate')
            ->with('test@example.com', [new Uuid()])
            ->willReturn(new ConstraintViolationList([$this->createStub(ConstraintViolation::class)]));

        self::assertSame('email', $this->guesser->getIdentifierType('test@example.com'));
    }

    public function testItResolvesPhoneNumberIfInvalidUidIsProvidedAndPhoneNumberIsTheIdentifier(): void
    {
        $this->settings->expects(self::once())
            ->method('getFederatedIdentityType')
            ->willReturn(FederatedIdentityType::PHONE_NUMBER);

        $this->validator->expects(self::once())
            ->method('validate')
            ->with('+1234567789', [new Uuid()])
            ->willReturn(new ConstraintViolationList([$this->createStub(ConstraintViolation::class)]));

        self::assertSame('phone_number', $this->guesser->getIdentifierType('+1234567789'));
    }
}
