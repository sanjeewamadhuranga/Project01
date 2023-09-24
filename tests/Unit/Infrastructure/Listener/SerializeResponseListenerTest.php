<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Listener;

use App\Infrastructure\Listener\SerializeResponseListener;
use App\Tests\Unit\UnitTestCase;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class SerializeResponseListenerTest extends UnitTestCase
{
    public function testItReturnsJsonResponseOnValidationFailedException(): void
    {
        $exception = $this->createMock(ValidationFailedException::class);

        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            $this->createStub(Request::class),
            0,
            $exception
        );

        $subscriber = $this->getSubscriber($exception);
        $subscriber->onKernelException($event);

        self::assertTrue($event->getResponse() instanceof JsonResponse);
    }

    /**
     * @dataProvider responseProvider
     */
    public function testItTransformToJsonResponseIfNotResponse(mixed $response): void
    {
        $event = new ViewEvent(
            $this->createStub(HttpKernelInterface::class),
            $this->createStub(Request::class),
            0,
            $response
        );

        $subscriber = $this->getSubscriber($response);
        $subscriber->onKernelView($event);

        self::assertTrue($event->getResponse() instanceof JsonResponse);
        self::assertSame(json_encode($response, JSON_THROW_ON_ERROR), $event->getResponse()->getContent());
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public function responseProvider(): iterable
    {
        yield 'zero' => [0];
        yield 'int' => [1];
        yield 'null' => [null];
        yield 'object' => [new stdClass()];
        yield 'string' => ['string'];
        yield 'array' => [[1, 2, 3]];
    }

    private function getSubscriber(mixed $data): SerializeResponseListener
    {
        $serializer = $this->createStub(SerializerInterface::class);
        $serializer->method('serialize')->willReturn(json_encode($data, JSON_THROW_ON_ERROR));

        return new SerializeResponseListener($serializer);
    }
}
