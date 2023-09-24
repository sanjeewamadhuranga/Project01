<?php

declare(strict_types=1);

use App\Application\Settings\Parameter;
use App\Infrastructure\HealthCheck\AwsCredentials;
use App\Infrastructure\HealthCheck\ConfigurationCheck;
use App\Infrastructure\HealthCheck\EnableFeatureCheck;
use App\Infrastructure\HealthCheck\FileExists;
use App\Infrastructure\HealthCheck\NotEmptyValue;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire();

    $services->set('monitor.check.file_exists.wkhtmltopdf', FileExists::class)
        ->call('setLabel', ['WKHTMLTOPDF Exits'])
        ->tag('liip_monitor.check', ['alias' => 'file_wkhtmltopdf', 'group' => 'private'])
        ->args([param(Parameter::WKHTMLTOPDF_PATH)]);

    $services->set('monitor.check.check_empty_sqs', NotEmptyValue::class)
        ->tag('liip_monitor.check', ['alias' => 'check_empty_sqs', 'group' => 'private'])
        ->args([param(Parameter::TOPIC_ARN), 'Check SQS values']);

    $services->set('monitor.check.check_empty_queue_url', NotEmptyValue::class)
        ->tag('liip_monitor.check', ['alias' => 'check_empty_queue_url', 'group' => 'private'])
        ->args([param(Parameter::QUEUE_URLS), 'Check queue urls']);

    $services->set('monitor.check.check_cloudinary_config', NotEmptyValue::class)
        ->tag('liip_monitor.check', ['alias' => 'check_cloudinary_config', 'group' => 'private'])
        ->args([param(Parameter::CLOUDINARY_CONFIG), 'Check cloudinary config']);

    $services->set('monitor.check.check_mailing_info', NotEmptyValue::class)
        ->tag('liip_monitor.check', ['alias' => 'check_mailing_information', 'group' => 'private'])
        ->args([param(Parameter::MAIL_SUBSCRIBER), 'Check requirements for mailing']);

    $services->set('monitor.check.check_other_api_keys', NotEmptyValue::class)
        ->tag('liip_monitor.check', ['alias' => 'check_other_api_keys', 'group' => 'private'])
        ->args([
            [
                Parameter::INTERCOM_API_KEY => param(Parameter::INTERCOM_API_KEY),
                Parameter::ONFIDO_API_KEY => param(Parameter::ONFIDO_API_KEY),
                Parameter::WECHATPAY_VENDOR => param(Parameter::WECHATPAY_VENDOR),
                Parameter::COGNITO_POOL_ID => param(Parameter::COGNITO_POOL_ID),
                Parameter::AWS_REGION => param(Parameter::AWS_REGION),
                Parameter::AWS_COGNITO_APP_ID => param(Parameter::AWS_COGNITO_APP_ID),
                Parameter::AWS_COGNITO_APP_KEY => param(Parameter::AWS_COGNITO_APP_KEY),
            ],
            'Check necessary API Keys',
        ]);

    $services->set('monitor.check.google_maps', NotEmptyValue::class)
        ->tag('liip_monitor.check', ['alias' => 'check_google_maps', 'group' => 'private'])
        ->args([[Parameter::GOOGLE_MAP_API_KEY => param(Parameter::GOOGLE_MAP_API_KEY)], 'Check Google Maps API Key']);

    $services->set('monitor.check.', AwsCredentials::class)
        ->tag('liip_monitor.check', ['alias' => 'check_aws_credentials', 'group' => 'private'])
        ->args([service('async_aws.credential')]);

    $services->set('monitor.check.check_configuration_setting', ConfigurationCheck::class)
        ->tag('liip_monitor.check', ['alias' => 'check_configuration_setting', 'group' => 'private']);

    $services->set('monitor.check.check_enable_feature_setting', EnableFeatureCheck::class)
        ->tag('liip_monitor.check', ['alias' => 'check_enable_feature_setting', 'group' => 'private']);
};
