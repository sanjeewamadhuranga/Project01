<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Configuration\Setting;

use App\Domain\Settings\Features;
use App\Domain\Settings\SettingsInterface;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Configuration\Settings\ManageFeaturesType;
use App\Infrastructure\Form\DataTransformer\StringToBooleanDataTransformer;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\HttpFoundation\ServerBag;

class ManageFeaturesTypeTest extends BaseSystemSettingTest
{
    private const DUMMY_FEATURE = 'feature.non-existent.dummy-feature';
    private const TOGGLEABLE_SETTINGS = [
        SystemSettings::SHOW_REGISTRATION_PROVISION,
        SystemSettings::SHOW_OFFERS,
        SystemSettings::SHOW_COMPLIANCE,
        SystemSettings::SHOW_AUTOCREDITS,
        SystemSettings::PAYOUT_REPORT,
        SystemSettings::PLATFORM_BILLING,
        SystemSettings::SHOW_FX_SETTLEMENT_LIST,
        SystemSettings::SHOW_EDC_IMPORT,
        SystemSettings::SHOW_PROVISION_MERCHANT,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->request->server = new ServerBag(['REMOTE_ADDR' => 'test']);
    }

    public function testItIsTransformingTheBooleanValueToString(): void
    {
        $form = $this->factory->create(ManageFeaturesType::class)->submit([
            SystemSettings::ENABLED_FEATURES => [Features::LOGIN_FORM, Features::APP_INTRO],
            SystemSettings::DISABLE_MANAGER_PORTAL_PASSWORD_LOGIN => true,
        ]);

        self::assertSame([Features::APP_INTRO, Features::LOGIN_FORM], array_values($form->getData()[SystemSettings::ENABLED_FEATURES]));
        self::assertSame('true', $form->getData()[SystemSettings::DISABLE_MANAGER_PORTAL_PASSWORD_LOGIN]);

        $form = $this->factory->create(ManageFeaturesType::class)->submit([
            SystemSettings::ENABLED_FEATURES => [Features::LOGIN_FORM, Features::APP_INTRO],
            SystemSettings::DISABLE_MANAGER_PORTAL_PASSWORD_LOGIN => null,
        ]);

        self::assertSame('false', $form->getData()[SystemSettings::DISABLE_MANAGER_PORTAL_PASSWORD_LOGIN]);
    }

    public function testItProvideIncorrectFeaturesWillReturnFalse(): void
    {
        $form = $this->factory->create(ManageFeaturesType::class);
        $submit = $form->submit([SystemSettings::ENABLED_FEATURES => ['nothing']]);

        $errors = $submit->getErrors(true);
        self::assertCount(1, $errors);
        /** @var FormError $error */
        $error = $errors[0];
        self::assertInstanceOf(TransformationFailedException::class, $error->getCause());
        self::assertFalse($submit->isValid());
    }

    public function testItAddsStringToBooleanTransformer(): void
    {
        $form = $this->factory->create(ManageFeaturesType::class);

        foreach (array_merge(self::TOGGLEABLE_SETTINGS, [SettingsInterface::DISABLE_MANAGER_PORTAL_PASSWORD_LOGIN]) as $setting) {
            self::assertInstanceOf(StringToBooleanDataTransformer::class, $form->get($setting)->getConfig()->getModelTransformers()[0]);
        }
    }

    public function testItHasCorrectDefaultValues(): void
    {
        $form = $this->factory->create(ManageFeaturesType::class);

        self::assertEqualsCanonicalizing(array_diff(array_values(Features::getConstants()), [Features::LOGIN_FORCE_2FA]), $form->get(SystemSettings::ENABLED_FEATURES)->getData());
        foreach (self::TOGGLEABLE_SETTINGS as $setting) {
            self::assertSame('true', $form->get($setting)->getData());
        }
    }

    public function testItAllowsToPassAnyExtraFeatureFromDatabase(): void
    {
        $form = $this->factory->create(ManageFeaturesType::class, [
            SystemSettings::ENABLED_FEATURES => [Features::LOGIN_FORM, Features::APP_INTRO, self::DUMMY_FEATURE],
        ])->submit([
            SystemSettings::ENABLED_FEATURES => [Features::LOGIN_FORM, Features::APP_INTRO, self::DUMMY_FEATURE],
        ]);

        $choices = array_map(
            static fn (ChoiceView $choice) => $choice->value,
            $form->createView()[SystemSettings::ENABLED_FEATURES]->vars['choices']
        );

        self::assertContains(self::DUMMY_FEATURE, $choices);
        self::assertTrue($form->isValid());
        self::assertEqualsCanonicalizing(
            [Features::LOGIN_FORM, Features::APP_INTRO, self::DUMMY_FEATURE],
            $form->get(SystemSettings::ENABLED_FEATURES)->getData()
        );
    }

    public function testItDoesNotAllowAnyExtraFeaturesToBePassedWhenSubmitting(): void
    {
        $form = $this->factory->create(ManageFeaturesType::class, [
            SystemSettings::ENABLED_FEATURES => [Features::LOGIN_FORM, Features::APP_INTRO],
        ])->submit([
            SystemSettings::ENABLED_FEATURES => [Features::LOGIN_FORM, Features::APP_INTRO, self::DUMMY_FEATURE],
        ]);

        $choices = array_map(
            static fn (ChoiceView $choice) => $choice->value,
            $form->createView()[SystemSettings::ENABLED_FEATURES]->vars['choices']
        );

        self::assertNotContains(self::DUMMY_FEATURE, $choices);
        self::assertFalse($form->isValid());
        self::assertNotContains(self::DUMMY_FEATURE, $form->get(SystemSettings::ENABLED_FEATURES)->getData());
    }

    /**
     * @return array<int, PreloadedExtension>
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new ManageFeaturesType($this->settings, $this->requestStack)], []),
        ];
    }
}
