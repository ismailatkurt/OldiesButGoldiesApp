<?php

use App\Controller\ArtistsController;
use App\Controller\RecordsController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('artists_index', '/artists')
        ->controller([ArtistsController::class, 'index'])
        ->methods(['GET']);
    $routes->add('artists_one', '/artists/{id}')
        ->controller([ArtistsController::class, 'one'])
        ->methods(['GET']);
    $routes->add('artists_create', '/artists')
        ->controller([ArtistsController::class, 'create'])
        ->methods(['POST']);

    $routes->add('records_index', '/records')
        ->controller([RecordsController::class, 'index'])
        ->methods(['GET']);
    $routes->add('records_one', '/records/{id}')
        ->controller([RecordsController::class, 'one'])
        ->methods(['GET']);
    $routes->add('records_create', '/records')
        ->controller([RecordsController::class, 'create'])
        ->methods(['POST']);
    $routes->add('records_update', '/records/{id}')
        ->controller([RecordsController::class, 'update'])
        ->methods(['PUT']);
    $routes->add('records_delete', '/records/{id}')
        ->controller([RecordsController::class, 'delete'])
        ->methods(['DELETE']);
};
