<?php

use OldiesButGoldiesApp\Record\Application\Http\Actions\Record\Index;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function(RoutingConfigurator $routes)
{
    $routes->add('records_index', '/records')
        ->controller([Index::class, 'execute'])
        ->methods(['GET']);
};
