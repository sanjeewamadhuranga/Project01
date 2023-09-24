<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\DataGrid;

use App\Domain\Document\Setting;
use App\Domain\Settings\SettingsInterface;
use App\Infrastructure\DataGrid\SettingList;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

class SettingListTest extends UnitTestCase
{
    public function testSettingListDataTransformerWillReturnCorrectDescription(): void
    {
        $settings = new Setting();
        $settings->setValue('TEST_VALUE');
        $settings->setId('test-id1');
        $settings->setName(SettingsInterface::ENABLED_TIMEZONES);

        self::assertSame([
            'id' => 'test-id1',
              'deleted' => false,
              'name' => SettingsInterface::ENABLED_TIMEZONES,
              'value' => 'TEST_VALUE',
              'description' => 'TEST_DESCRIPTION',
              'createdAt' => null,
              'updatedAt' => null,
        ], $this->getList()->transform($settings, 0));
    }

    public function testSettingListDataTransformerWillReturnNullDescription(): void
    {
        $settings = new Setting();
        $settings->setValue('TEST_VALUE');
        $settings->setMaskValue(true);
        $settings->setId('test-id2');
        $settings->setName(SettingsInterface::ADMIN_THEME);

        self::assertSame([
            'id' => 'test-id2',
            'deleted' => false,
            'name' => SettingsInterface::ADMIN_THEME,
            'value' => '**********',
            'description' => null,
            'createdAt' => null,
            'updatedAt' => null,
        ], $this->getList()->transform($settings, 0));
    }

    private function getList(): SettingList
    {
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', [
            'configuration.settings.description.ENABLED_TIMEZONES' => 'TEST_DESCRIPTION',
        ], 'en', 'settings');

        return new SettingList($this->createStub(DocumentManager::class), $translator);
    }
}
