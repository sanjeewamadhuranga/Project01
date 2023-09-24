<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('../src/Http/Controller/', 'attribute');
    $routingConfigurator->add('google_login', '/login/check-google');
    $routingConfigurator->add('logout', '/logout');
};
