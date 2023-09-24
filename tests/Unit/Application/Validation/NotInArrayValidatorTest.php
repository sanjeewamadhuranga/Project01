<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Validation;

use App\Application\Validation\Company\PhoneNumber;
use App\Application\Validation\NotInArray;
use App\Application\Validation\NotInArrayValidator;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotInArrayValidatorTest extends UnitTestCase
{
    private NotInArrayValidator $validator;

    private ExecutionContextInterface&MockObject $context;

    protected function setUp(): void
    {
        $this->validator = new NotInArrayValidator();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($this->context);
    }

    public function testItBuildsViolationWhenValueThatAlreadyInArrayProvided(): void
    {
        $this->context->expects(self::once())->method('buildViolation');

        $this->validator->validate('aaa', $this->getConstraint());
    }

    public function testItDoesNotBuildViolationWhenNewValueProvided(): void
    {
        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->validate('ddd', $this->getConstraint());
    }

    public function testItThrowsExceptionWhenWrongConstraintProvided(): void
    {
        $constraint = new PhoneNumber();

        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage(sprintf('Expected argument of type "%s", "%s" given', NotInArray::class, get_debug_type($constraint)));

        $this->validator->validate('eee', $constraint);
    }

    private function getConstraint(): Constraint
    {
        return new NotInArray(['aaa', 'bbb', 'ccc']);
    }
}
