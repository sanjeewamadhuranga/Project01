<?php

declare(strict_types=1);

namespace App\Infrastructure\Sentry\MongoDB;

use Doctrine\ODM\MongoDB\APM\Command;
use Doctrine\ODM\MongoDB\APM\CommandLoggerInterface;
use JsonException;

use function MongoDB\Driver\Monitoring\addSubscriber;

use MongoDB\Driver\Monitoring\CommandFailedEvent;
use MongoDB\Driver\Monitoring\CommandStartedEvent;
use MongoDB\Driver\Monitoring\CommandSucceededEvent;

use function MongoDB\Driver\Monitoring\removeSubscriber;

use Sentry\Breadcrumb;
use Sentry\State\HubInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AutoconfigureTag('doctrine_mongodb.odm.command_logger')]
#[When('prod')]
final class CommandLogger implements CommandLoggerInterface
{
    /** @var array<string, CommandStartedEvent> */
    private array $startedCommands = [];

    private bool $registered = false;

    public function __construct(private readonly HubInterface $hub)
    {
    }

    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        $this->registered = true;
        addSubscriber($this);
    }

    public function unregister(): void
    {
        if (!$this->registered) {
            return;
        }

        removeSubscriber($this);
        $this->registered = false;
    }

    public function commandStarted(CommandStartedEvent $event): void
    {
        $this->startedCommands[$event->getRequestId()] = $event;
    }

    public function commandSucceeded(CommandSucceededEvent $event): void
    {
        $commandStartedEvent = $this->findAndRemoveCommandStartedEvent($event->getRequestId());
        if (null === $commandStartedEvent) {
            return;
        }

        $this->logCommand(Command::createForSucceededCommand($commandStartedEvent, $event));
    }

    public function commandFailed(CommandFailedEvent $event): void
    {
        $commandStartedEvent = $this->findAndRemoveCommandStartedEvent($event->getRequestId());
        if (null === $commandStartedEvent) {
            return;
        }

        $this->logCommand(Command::createForFailedCommand($commandStartedEvent, $event));
    }

    private function findAndRemoveCommandStartedEvent(string $requestId): ?CommandStartedEvent
    {
        $startedEvent = $this->startedCommands[$requestId] ?? null;
        unset($this->startedCommands[$requestId]);

        return $startedEvent;
    }

    private function logCommand(Command $command): void
    {
        try {
            $this->hub->addBreadcrumb(new Breadcrumb(
                Breadcrumb::LEVEL_DEBUG,
                null === $command->getError() ? Breadcrumb::TYPE_DEFAULT : Breadcrumb::TYPE_ERROR,
                'mongodb.command',
                json_encode($command->getCommand(), JSON_THROW_ON_ERROR),
                [
                    'type' => $command->getCommandName(),
                    'duration' => $command->getDurationMicros(),
                ],
            ));
        } catch (JsonException) {
        }
    }
}
