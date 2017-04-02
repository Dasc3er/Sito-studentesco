<?php

$app->group('/admin', function () use ($app) {
    $app->get('', 'App\Controllers\AdminController:index')->setName('administration');
    $app->get('/', 'App\Controllers\AdminController:index');

    $app->group('/logins', function () use ($app) {
        $app->get('', 'App\Controllers\AdminController:logins')->setName('logins');
        $app->get('/reset', 'App\Controllers\AdminController:resetlogins')->setName('reset-logins');
    });

    $app->group('/visits', function () use ($app) {
        $app->get('', 'App\Controllers\AdminController:visits')->setName('visits');
        $app->get('/reset', 'App\Controllers\AdminController:resetVisits')->setName('reset-visits');
    });

    $app->group('/upload', function () use ($app) {
        $app->get('', 'App\Controllers\AdminController:upload')->setName('upload');
        $app->post('', 'App\Controllers\AdminController:uploadPost')->setName('upload-post');
    });

})->add('App\Middlewares\Permissions\AdminMiddleware');
