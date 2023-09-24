<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Sentry\MongoDB;

use App\Infrastructure\Sentry\MongoDB\CommandLogger;
use App\Tests\Unit\UnitTestCase;
use ReflectionClass;
use Sentry\State\HubInterface;

class CommandLoggerTest extends UnitTestCase
{
    private HubInterface $hub;

    private CommandLogger $commandLogger;

    protected function setUp(): void
    {
        $this->hub = $this->createMock(HubInterface::class);
        $this->commandLogger = new CommandLogger($this->hub);

        parent::setUp();
    }

    public function testItRegistersAndUnregisters(): void
    {
        $reflectionClass = new ReflectionClass(CommandLogger::class);
        $reflectionProperty = $reflectionClass->getProperty('registered');
        $reflectionProperty->setAccessible(true);

        self::assertFalse($reflectionProperty->getValue($this->commandLogger));

        $this->commandLogger->register();
        self::assertTrue($reflectionProperty->getValue($this->commandLogger));

        $this->commandLogger->unregister();
        self::assertFalse($reflectionProperty->getValue($this->commandLogger));
    }
}
