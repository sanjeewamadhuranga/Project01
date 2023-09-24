<?php

declare(strict_types=1);

namespace App\Tests\Unit\Migration\Command;

use App\Domain\Document\Migration;
use App\Infrastructure\Repository\MigrationRepository;
use App\Migration\Command\StatusCommand;
use App\Migration\Migration\AbstractMigration;
use App\Migration\MigrationManager;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;

class StatusCommandTest extends UnitTestCase
{
    public function testThereAreMigrationsListedAndCountedInOutput(): void
    {
        $executedAndAvailableMigration = '20220119031753';

        $fileMigrations = new ArrayCollection([
            $executedAndAvailableMigration => $this->createStub(AbstractMigration::class),
            '20220126142935' => $this->createStub(AbstractMigration::class),
        ]);

        $migrationRepository = $this->createStub(MigrationRepository::class);
        $migrationRepository->method('findAll')->willReturn([
            new Migration($executedAndAvailableMigration),
            new Migration('20220119031754'),
            new Migration('20220119031755'),
            new Migration('20220119031756'),
        ]);

        $command = new StatusCommand(new MigrationManager($fileMigrations, $migrationRepository));
        $command->setHelperSet(new HelperSet(['formatter' => new FormatterHelper()]));

        $tester = new CommandTester($command);
        $tester->execute([]);

        self::assertMatchesRegularExpression('/'.$executedAndAvailableMigration.'(\s*)Yes(\s*)Yes/', $tester->getDisplay());
        self::assertMatchesRegularExpression('/20220126142935(\s*)No(\s*)Yes/', $tester->getDisplay());
        self::assertMatchesRegularExpression('/20220119031754(\s*)Yes(\s*)No/', $tester->getDisplay());
        self::assertMatchesRegularExpression('/20220119031755(\s*)Yes(\s*)No/', $tester->getDisplay());
        self::assertMatchesRegularExpression('/20220119031756(\s*)Yes(\s*)No/', $tester->getDisplay());

        self::assertMatchesRegularExpression('/Migrations executed:(\s*)4/', $tester->getDisplay());
        self::assertMatchesRegularExpression('/Migrations unavailable:(\s*)3/', $tester->getDisplay());
        self::assertMatchesRegularExpression('/Migrations available:(\s*)2/', $tester->getDisplay());
        self::assertMatchesRegularExpression('/Migrations new:(\s*)1/', $tester->getDisplay());
    }
}
