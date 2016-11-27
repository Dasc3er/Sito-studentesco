<?php

$app->group('/admin', function () use ($app, $permissions) {
    $app->get('', 'App\Controllers\AdminController:index')->setName('administration');
    $app->get('/', 'App\Controllers\AdminController:index');

    $app->group('/logins', function () use ($app, $permissions) {
        $app->get('', 'App\Controllers\AdminController:logins')->setName('accessi');
        $app->get('/reset', 'App\Controllers\AdminController:resetlogins')->setName('reset-accessi');
    });

    $app->group('/visits', function () use ($app, $permissions) {
        $app->get('', 'App\Controllers\AdminController:visits')->setName('visite');
        $app->get('/reset', 'App\Controllers\AdminController:resetVisits')->setName('reset-visite');
    });

})->add($permissions['admin']);
