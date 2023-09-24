<?php

declare(strict_types=1);

namespace App\Infrastructure\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class SerializeResponseListener
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    #[AsEventListener]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationFailedException) {
            $event->setResponse($this->toResponse($exception->getViolations(), Response::HTTP_UNPROCESSABLE_ENTITY));
            $event->allowCustomResponseCode();
        }
    }

    #[AsEventListener]
    public function onKernelView(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        if ($result instanceof Response) {
            return;
        }

        $event->setResponse($this->toResponse($result));
    }

    private function toResponse(mixed $data, int $code = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($this->serializer->serialize($data, 'json'), $code, [], true);
    }
}
