<?php

declare(strict_types=1);

use App\Application\DataFixtures\MongoDB\AppFixtures;
use App\Application\DataGrid\DataGridHandlerInterface;
use App\Application\Listener\ActivityLogListener;
use App\Application\Settings\Parameter;
use App\Application\Setup\AdministratorAccountSetupWizardDetector;
use App\Domain\Company\LocationStatus;
use App\Domain\Company\ReviewStatus;
use App\Domain\Company\Type as CompanyType;
use App\Domain\Company\UserStatus;
use App\Domain\Compliance\DisputeReason;
use App\Domain\Compliance\DisputeState;
use App\Domain\Settings\Features;
use App\Domain\Settings\SystemSettings;
use App\Domain\Transaction\HistoricalStatus;
use App\Domain\Transaction\Provider as TransactionProvider;
use App\Domain\Transaction\RemittanceStatus;
use App\Domain\Transaction\Status as TransactionStatus;
use App\Http\Controller\HealthCheckController;
use App\Infrastructure\DataGrid\DataGridHandler;
use App\Infrastructure\DataGrid\Debug\TraceableDataGridHandler;
use App\Infrastructure\DataGrid\SettingList;
use App\Infrastructure\Faker\EnumProvider;
use App\Infrastructure\Listener\SetFromMailListener;
use App\Infrastructure\Menu\ConfigurationMenu;
use App\Infrastructure\Menu\SideMenu;
use App\Infrastructure\Notifier\OneSignalTransportFactory;
use App\Infrastructure\Service\UploadService;
use App\Infrastructure\Storage\S3Signer;
use App\Infrastructure\Storage\Upload\CleanListener;
use App\Infrastructure\Storage\Upload\CompanyFileNamer;
use App\Infrastructure\Twig\MenuExtension;
use Cloudinary\Cloudinary;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry as MongoManagerRegistry;
use Doctrine\Persistence\ManagerRegistry;
use Intercom\IntercomClient;
use Liip\MonitorBundle\Helper\RunnerManager;
use Onfido\Api\DefaultApi;
use Onfido\Configuration;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

use Symfony\Component\Serializer\Normalizer\CustomNormalizer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__.'/parameters.php');
    $containerConfigurator->import(__DIR__.'/health_checks.php');
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$weChatPayVendor', param(Parameter::WECHATPAY_VENDOR))
        ->bind('$userPoolId', param(Parameter::COGNITO_POOL_ID))
        ->bind('$defaultLocale', param('kernel.default_locale'))
        ->bind('$availableLocales', param('kernel.enabled_locales'))
        ->bind('string $environment', param('kernel.environment'))
        ->bind('$queueUrls', param(Parameter::QUEUE_URLS))
        ->bind('$topicArns', param(Parameter::TOPIC_ARN))
        ->bind(RunnerManager::class, service('liip_monitor.helper.runner_manager'))
        ->bind('$publicDir', '%kernel.project_dir%/public')
    ;

    $services->load('App\\', __DIR__.'/../src/*')
        ->exclude([
            __DIR__.'/../src/DependencyInjection/',
            __DIR__.'/../src/Entity/',
            __DIR__.'/../src/Document/',
            __DIR__.'/../src/Domain/Document/',
            __DIR__.'/../src/Kernel.php',
            __DIR__.'/../src/Tests/',
            __DIR__.'/../src/Application/Security/CognitoUser.php',
            __DIR__.'/../src/Application/DTO/',
            __DIR__.'/../src/Http/Model/',
            __DIR__.'/../src/Application/DataFixtures/',
            __DIR__.'/../src/Infrastructure/HealthCheck/',
        ]);

    $services->set(HealthCheckController::class)->arg(0, service('liip_monitor.helper.runner_manager'));
    $services->set(CustomNormalizer::class);
    $services->load('App\\Http\\Controller\\', __DIR__.'/../src/Http/Controller/*')
        ->tag('controller.service_arguments');

    $services->alias(ManagerRegistry::class, MongoManagerRegistry::class);

    $services->set(SetFromMailListener::class)
        ->arg('$fromEmail', param(Parameter::FROM_EMAIL))
        ->arg('$senderName', param(Parameter::SENDER_NAME));

    $services->set(SettingList::class)->arg('$translator', service('translator'));

    $services->alias(DataGridHandlerInterface::class, DataGridHandler::class);

    $services->alias('settings', SystemSettings::class);
    $services->alias('features', Features::class);

    $services->set('sensio_framework_extra.converter.doctrine.mongodb', DoctrineParamConverter::class)
        ->args([service('doctrine_mongodb')])
        ->tag('request.param_converter', ['converter' => 'doctrine.mongodb']);

    $services->set(CompanyFileNamer::class)->public();

    $services->set(Cloudinary::class)->arg('$config', [
        'cloud' => param(Parameter::CLOUDINARY_CONFIG),
    ]);

    $services->set('notifier.transport_factory.onesignal', OneSignalTransportFactory::class)
        ->parent('notifier.transport_factory.abstract')
        ->tag('texter.transport_factory');

    // Fixes VichUploader issue: https://github.com/dustin10/VichUploaderBundle/issues/1263 by replacing the listener with a custom one.
    $services->set(CleanListener::class)
        ->parent('vich_uploader.listener.doctrine.base')
        ->alias('vich_uploader.listener.clean.orm', CleanListener::class);

    $services->set(Configuration::class)
        ->call('setApiKey', ['Authorization', 'token=%'.Parameter::ONFIDO_API_KEY.'%'])
        ->call('setApiKeyPrefix', ['Authorization', 'Token']);

    $services->set(DefaultApi::class)->args([null, service(Configuration::class)]);

    $services->set(IntercomClient::class)->args([param(Parameter::INTERCOM_API_KEY)]);

    $services->set(S3Signer::class)
        ->arg('$buckets', param(Parameter::S3_BUCKETS))
    ;

    $services->set(MenuExtension::class)
        ->arg('$menus', [
            'sideMenu' => service(SideMenu::class),
            'configMenu' => service(ConfigurationMenu::class),
        ]);

    $services->set(AdministratorAccountSetupWizardDetector::class)->arg('$enableSetupWizard', param(Parameter::ENABLE_SETUP_WIZARD));

    $services->set(UploadService::class)
        ->arg('$metadata', service('vich_uploader.metadata_reader'));

    if ('test' !== $containerConfigurator->env()) {
        $services->set(ActivityLogListener::class)
            ->tag('doctrine_mongodb.odm.event_listener', ['event' => 'onFlush'])
            ->tag('doctrine_mongodb.odm.event_listener', ['event' => 'postFlush']);
    }

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->import(__DIR__.'/services_test.php');
    }

    if (in_array($containerConfigurator->env(), ['dev', 'test'], true)) {
        $services->set(AppFixtures::class)->args([service('fidry_alice_data_fixtures.loader.doctrine_mongodb')]);
        $services->alias(DataGridHandlerInterface::class, TraceableDataGridHandler::class);

        $services->set(EnumProvider::class)->arg(0, [
            'TransactionStatus' => TransactionStatus::class,
            'TransactionHistoricalStatus' => HistoricalStatus::class,
            'CompanyStatus' => ReviewStatus::class,
            'CompanyType' => CompanyType::class,
            'CompanyUserStatus' => UserStatus::class,
            'RemittanceStatus' => RemittanceStatus::class,
            'TransactionProvider' => TransactionProvider::class,
            'DisputeState' => DisputeState::class,
            'DisputeReason' => DisputeReason::class,
            'LocationStatus' => LocationStatus::class,
        ])->tag('nelmio_alice.faker.provider');
    }
};
