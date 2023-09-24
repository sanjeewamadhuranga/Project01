<?php

declare(strict_types=1);

namespace App\Tests\Feature\Traits;

use App\Application\Queue\Bus;
use App\Application\Queue\Queueable;
use App\Infrastructure\Queue\CommandDispatcher;
use App\Infrastructure\Queue\MessageDispatcher;
use Happyr\ServiceMocking\ServiceMock;

trait MessageBusTrait
{
    protected Bus $bus;

    public function replaceBus(): void
    {
        // @phpstan-ignore-next-line
        $this->bus = new class($this->createStub(CommandDispatcher::class), $this->createStub(MessageDispatcher::class)) extends Bus {
            public function dispatch(Queueable $queueable): void
            {
            }
        };

        ServiceMock::swap(self::$client->getContainer()->get(Bus::class), $this->bus);
    }
}
