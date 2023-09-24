<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Setup;

use App\Application\Setup\SystemSettingsFormSaver;
use App\Domain\Document\Setting;
use App\Domain\Settings\SystemSettings;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;

use function PHPUnit\Framework\never;
use function PHPUnit\Framework\once;

use Symfony\Component\Form\FormInterface;

class SystemSettingsFormSaverTest extends UnitTestCase
{
    private SystemSettings&Stub $systemSettings;

    private DocumentManager&MockObject $documentManager;

    private SystemSettingsFormSaver $saver;

    protected function setUp(): void
    {
        $this->systemSettings = $this->createStub(SystemSettings::class);

        $this->documentManager = $this->createMock(DocumentManager::class);

        $this->saver = new SystemSettingsFormSaver($this->systemSettings, $this->documentManager);
    }

    public function testItSavesDataFromForm(): void
    {
        $settingName = 'someName';
        $settingValue = 'someValue';

        $form = $this->createStub(FormInterface::class);
        $form->method('getData')->willReturn([$settingName => $settingValue]);

        $this->documentManager->expects(once())->method('persist')->with(self::callback(fn (Setting $setting) => $settingName === $setting->getName() && $settingValue === $setting->getValue()));
        $this->documentManager->expects(once())->method('flush');

        $this->saver->saveSettings($form);
    }

    public function testItDoNotSavesEmptyValues(): void
    {
        $form = $this->createStub(FormInterface::class);
        $form->method('getData')->willReturn(['settingName' => null]);

        $this->documentManager->expects(never())->method('persist');

        $this->saver->saveSettings($form);
    }

    public function testItChangesAndSavesValueOfSettingWhichWasFoundInDb(): void
    {
        $settingName = 'testName';
        $newSettingValue = 'secondValue';
        $setting = new Setting($settingName);
        $setting->setValue('firstValue');

        $this->systemSettings->method('get')->willReturn($setting);

        $form = $this->createStub(FormInterface::class);
        $form->method('getData')->willReturn([$settingName => $newSettingValue]);

        $this->documentManager->expects(once())->method('persist')->with($setting);

        $this->saver->saveSettings($form);

        self::assertSame($newSettingValue, $setting->getValue());
    }
}
