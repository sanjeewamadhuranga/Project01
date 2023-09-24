<?php

declare(strict_types=1);

use App\Application\Company\Intercom;
use App\Application\Compliance\Onfido;
use App\Infrastructure\Security\CognitoUserManager;
use App\Tests\Mock\Application\Company\IntercomMock;
use App\Tests\Mock\Application\Compliance\OnfidoMock;
use App\Tests\Mock\Infrastructure\Security\CognitoUserManagerMock;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(CognitoUserManager::class, CognitoUserManagerMock::class);
    $services->set(Intercom::class, IntercomMock::class);
    $services->set(Onfido::class, OnfidoMock::class);
};
