<?php

declare(strict_types=1);

use App\Application\Bucket\BucketName;
use App\Application\Queue\QueueName;
use App\Application\Queue\Topic;
use App\Application\Settings\Parameter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set(Parameter::FROM_EMAIL, env('MAILJET_REPLY'))
        ->set(Parameter::SENDER_NAME, env('SENDER_NAME'))

        // S3 buckets
        ->set(Parameter::BUCKET_MERCHANT, env('MERCHANT_BUCKET'))
        ->set(Parameter::BUCKET_TERMINAL, env('TERMINAL_BUCKET'))
        ->set(Parameter::BUCKET_CARD, env('CARD_BUCKET'))
        ->set(Parameter::BUCKET_DISCOUNT_RATE, env('DISCOUNTRATE_BUCKET'))
        ->set(Parameter::BUCKET_COMPLIANCE_REPORTS, env('COMPLIANCEREPORTS_BUCKET'))
        ->set(Parameter::BUCKET_ATTACHMENTS, env('ATTACHMENTS_BUCKET'))
        ->set(Parameter::BUCKET_MANAGEMENT_REPORTS, env('MANAGEMENT_REPORTS_BUCKET'))
        ->set(Parameter::BUCKET_TRANSACTION_REPORTS, env('TRANSACTION_REPORTS_BUCKET'))

        // AWS credentials
        ->set('aws_key', env('AWS_KEY'))
        ->set('aws_secret_key', env('AWS_SECRET_KEY'))
        ->set('aws_region', env('AWS_REGION'))

        // Cognito
        ->set(Parameter::COGNITO_POOL_ID, env('AWS_COGNITO_USER_POOL_ID'))
        ->set(Parameter::COGNITO_APP_ID, env('AWS_COGNITO_APP_ID'))
        ->set(Parameter::COGNITO_APP_KEY, env('AWS_COGNITO_APP_KEY'))

        // SNS Topics
        ->set(Parameter::SNS_TOPIC_BILLING_REPORT_RECALCULATE, env('SNS_TOPIC_BILLING_REPORT_RECALCULATE'))
        ->set(Parameter::SNS_TOPIC_CLEAR_CACHE, env('SNS_TOPIC_CLEAR_CACHE'))
        ->set(Parameter::SNS_TOPIC_MANAGEMENT_REPORT, env('SNS_TOPIC_MANAGEMENT_REPORT'))
        ->set(Parameter::SNS_TOPIC_PROVIDER_ONBOARDING, env('SNS_TOPIC_PROVIDER_ONBOARDING'))

        // SQS queue URLs
        ->set(Parameter::COMPANY_QUEUE_URL, env('COMPANY_QUEUE_URL'))
        ->set(Parameter::REFUND_QUEUE_URL, env('REFUND_QUEUE_URL'))
        ->set(Parameter::TRANSACTION_QUEUE_URL, env('TRANSACTION_QUEUE_URL'))
        ->set(Parameter::TRIGGER_QUEUE_URL, env('TRIGGER_QUEUE_URL'))
        ->set(Parameter::SMS_QUEUE_URL, env('SMS_QUEUE_URL'))

        // External services API keys
        ->set(Parameter::CLOUDINARY_BUCKET, env('CLOUDINARY_BUCKET'))
        ->set(Parameter::CLOUDINARY_API_KEY, env('CLOUDINARY_API_KEY'))
        ->set(Parameter::CLOUDINARY_API_SECRET, env('CLOUDINARY_API_SECRET'))
        ->set(Parameter::ONFIDO_API_KEY, env('ONFIDO_API_KEY'))
        ->set(Parameter::INTERCOM_API_KEY, env('INTERCOM_API_KEY'))
        ->set(Parameter::GOOGLE_MAP_API_KEY, env('GOOGLE_MAPS_API_KEY'))

        ->set('owner', env('setting:OWNER')) // Will be resolved at runtime with value from DB
        ->set('currencyLimits', env('setting:CURRENCY_LIMITS'))

        // Payment vendors
        ->set(Parameter::WECHATPAY_VENDOR, env('CLOUDINARY_BUCKET'))

        // Setup wizard
        ->set(Parameter::ENABLE_SETUP_WIZARD, env('ENABLE_SETUP_WIZARD')->bool())

        // WkHtmlToPdf
        ->set(Parameter::WKHTMLTOPDF_PATH, env('WKHTMLTOPDF_PATH'))

        // S3 buckets
        ->set(Parameter::S3_BUCKETS, [
            BucketName::COMPLIANCE->name => env('COMPLIANCEREPORTS_BUCKET'),
            BucketName::TRANSACTION->name => env('ATTACHMENTS_BUCKET'),
            BucketName::TRANSACTION_REPORTS->name => env('TRANSACTION_REPORTS_BUCKET'),
            BucketName::MANAGEMENT_REPORTS->name => env('MANAGEMENT_REPORTS_BUCKET'),
        ])

        // Topic Arn
        ->set(Parameter::TOPIC_ARN, [
            Topic::BILLING_REPORT_RECALCULATE->name => env('SNS_TOPIC_BILLING_REPORT_RECALCULATE'),
            Topic::CLEAR_CACHE->name => env('SNS_TOPIC_CLEAR_CACHE'),
            Topic::MANAGEMENT_REPORT->name => env('SNS_TOPIC_MANAGEMENT_REPORT'),
            Topic::PROVIDER_ONBOARDING->name => env('SNS_TOPIC_PROVIDER_ONBOARDING'),
        ])

        // Queue Url
        ->set(Parameter::QUEUE_URLS, [
            QueueName::COMPANY->name => env('COMPANY_QUEUE_URL'),
            QueueName::REFUND->name => env('REFUND_QUEUE_URL'),
            QueueName::TRANSACTION->name => env('TRANSACTION_QUEUE_URL'),
            QueueName::TRIGGER->name => env('TRIGGER_QUEUE_URL'),
            QueueName::SMS->name => env('SMS_QUEUE_URL'),
            QueueName::PLATFORM_COMPANY_UPDATE->name => env('PLATFORM_COMPANY_UPDATE_NOTIFY_QUEUE'),
            QueueName::PLATFORM_USER_UPDATE->name => env('PLATFORM_USER_UPDATE_NOTIFY_QUEUE'),
        ])

        // Cloudinary Config
        ->set(Parameter::CLOUDINARY_CONFIG, [
            'cloud_name' => env('CLOUDINARY_BUCKET'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
        ])

        // Email
        ->set(Parameter::MAIL_SUBSCRIBER, [
            '$fromEmail' => param(Parameter::FROM_EMAIL),
            '$senderName' => param(Parameter::SENDER_NAME),
        ])
    ;
};
