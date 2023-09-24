<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\ArgumentResolver;

use App\Infrastructure\Http\Attribute\RequestBody;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(private readonly SerializerInterface $serializer, private readonly ValidatorInterface $validator)
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return count($argument->getAttributes(RequestBody::class, ArgumentMetadata::IS_INSTANCEOF)) > 0;
    }

    /**
     * @return iterable<mixed>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (null === $argument->getType()) {
            throw new InvalidArgumentException(sprintf('Argument "%s" must have the type specified for the request body conversion.', $argument->getName()));
        }

        /** @var RequestBody $attribute */
        $attribute = $argument->getAttributes(RequestBody::class, ArgumentMetadata::IS_INSTANCEOF)[0];
        try {
            $value = $this->serializer->deserialize(
                $request->getContent(),
                $argument->getType(),
                'json',
                $attribute->context
            );
        } catch (UnexpectedValueException $e) {
            throw new BadRequestHttpException('Could not decode the request', $e);
        }

        $violations = $this->validator->validate($value, null, $attribute->validationGroups);

        if ($violations->count() > 0) {
            throw new ValidationFailedException($value, $violations);
        }

        yield $value;
    }
}
