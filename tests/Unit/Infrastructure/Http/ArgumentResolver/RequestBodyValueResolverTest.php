<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Http\ArgumentResolver;

use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Http\ArgumentResolver\RequestBodyValueResolver;
use App\Infrastructure\Http\Attribute\RequestBody;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;

use function PHPUnit\Framework\once;

use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Traversable;

class RequestBodyValueResolverTest extends UnitTestCase
{
    private RequestBodyValueResolver $resolver;

    private SerializerInterface&MockObject $serializer;

    private ValidatorInterface&Stub $validator;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createStub(ValidatorInterface::class);

        $this->resolver = new RequestBodyValueResolver($this->serializer, $this->validator);

        parent::setUp();
    }

    public function testItSupportsWhenThereAreAttributes(): void
    {
        $argument = $this->createStub(ArgumentMetadata::class);
        $argument->method('getAttributes')->willReturn([new stdClass()]);

        self::assertTrue($this->resolver->supports($this->createStub(Request::class), $argument));
    }

    public function testItDoesNotSupportWhenThereAreNoAttributes(): void
    {
        $argument = $this->createStub(ArgumentMetadata::class);
        $argument->method('getAttributes')->willReturn([]);

        self::assertFalse($this->resolver->supports($this->createStub(Request::class), $argument));
    }

    public function testItThrowsExceptionWhenNoTypeProvided(): void
    {
        $argumentName = 'myArgument';
        $argument = $this->createStub(ArgumentMetadata::class);
        $argument->method('getType')->willReturn(null);
        $argument->method('getName')->willReturn($argumentName);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(sprintf('Argument "%s" must have the type specified for the request body conversion.', $argumentName));

        $result = $this->resolver->resolve($this->createStub(Request::class), $argument);
        self::assertInstanceOf(Traversable::class, $result);
        iterator_to_array($result);
    }

    public function testItThrowsExceptionWhenThereAreViolations(): void
    {
        $requestBody = new RequestBody();

        $argument = $this->createStub(ArgumentMetadata::class);
        $argument->method('getType')->willReturn('string');
        $argument->method('getName')->willReturn('argument');
        $argument->method('getAttributes')->willReturn([$requestBody]);

        $this->serializer->method('deserialize')->willReturn('someValue');

        $violations = $this->createStub(ConstraintViolationList::class);
        $violations->method('count')->willReturn(5);
        $violations->method('__toString')->willReturn('violations');
        $this->validator->method('validate')->willReturn($violations);

        self::expectException(ValidationFailedException::class);

        $result = $this->resolver->resolve($this->createStub(Request::class), $argument);
        self::assertInstanceOf(Traversable::class, $result);
        iterator_to_array($result);
    }

    public function testItReturnsAttribute(): void
    {
        $context = ['a', 'b', 'c'];
        $attribute = uniqid();
        $argumentType = Administrator::class;
        $requestContent = 'requestContent';

        $requestBody = new RequestBody();
        $requestBody->context = $context;

        $argumentMetadata = new ArgumentMetadata('name', $argumentType, false, false, null, false, [$requestBody]);

        $this->serializer->expects(once())->method('deserialize')->with($requestContent, $argumentType, 'json', $context)->willReturn($attribute);

        $violations = $this->createStub(ConstraintViolationList::class);
        $violations->method('count')->willReturn(0);
        $this->validator->method('validate')->willReturn($violations);

        $request = $this->createStub(Request::class);
        $request->method('getContent')->willReturn($requestContent);

        $result = $this->resolver->resolve($request, $argumentMetadata);

        self::assertInstanceOf(Traversable::class, $result);
        self::assertSame([$attribute], iterator_to_array($result));
    }
}
