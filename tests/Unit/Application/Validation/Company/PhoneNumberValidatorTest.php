<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Validation\Company;

use App\Application\Validation\Company\PhoneNumber;
use App\Application\Validation\Company\PhoneNumberValidator;
use App\Tests\Unit\UnitTestCase;
use stdClass;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PhoneNumberValidatorTest extends UnitTestCase
{
    private PhoneNumberValidator $validator;

    private PhoneNumber $constraint;

    protected function setUp(): void
    {
        $this->validator = new PhoneNumberValidator();

        $this->constraint = new PhoneNumber();

        parent::setUp();
    }

    /**
     * @dataProvider valueWhichThrowsExceptionProvider
     */
    public function testItThrowsExceptionWhenValueIsNotString(mixed $value): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate($value, $this->constraint);
    }

    /**
     * @dataProvider wrongValueProvider
     */
    public function testItBuildsViolationWhenWrongValueProvided(string $value): void
    {
        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext->expects(self::once())->method('buildViolation')->with($this->constraint->message);

        $this->validator->initialize($executionContext);

        $this->validator->validate($value, $this->constraint);
    }

    /**
     * @dataProvider correctValueProvider
     */
    public function testItDoesNotBuildViolationWhenCorrectValueProvided(string $value): void
    {
        $executionContext = $this->createMock(ExecutionContextInterface::class);
        $executionContext->expects(self::never())->method('buildViolation');

        $this->validator->initialize($executionContext);

        $this->validator->validate($value, $this->constraint);
    }

    /**
     * @return iterable<array<int, mixed>>
     */
    public function valueWhichThrowsExceptionProvider(): iterable
    {
        yield [new stdClass()];
        yield [-1];
        yield [[]];
        yield [true];
    }

    /**
     * @return iterable<array<int, string>>
     */
    public function wrongValueProvider(): iterable
    {
        yield ['abcdefg'];
        yield ['1234567890'];
        yield ['+12'];
        yield ['+1234567890123456'];
        yield ['+0123'];
        yield ['+112a3'];
    }

    /**
     * @return iterable<array<int, string>>
     */
    public function correctValueProvider(): iterable
    {
        yield ['+48600700800'];
        yield ['+123456789012345'];
        yield ['+1123'];
    }
}
