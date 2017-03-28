<?php

$app->group('', function () use ($app, $permissions) {
    $app->group('/users', function () use ($app, $permissions) {
        $app->get('', 'App\Controllers\UserController:index')->setName('users');

        $app->get('/delete/{id:[0-9]+}', 'App\Controllers\UserController:delete')->setName('delete-user');
        $app->post('/delete/{id:[0-9]+}', 'App\Controllers\UserController:deletePost');

        $app->get('/enable/{id:[0-9]+}', 'App\Controllers\UserController:enable')->setName('enable-user');

        $app->get('/admin/{id:[0-9]+}', 'App\Controllers\UserController:admin')->setName('admin');
    })->add($permissions['admin']);

    $app->get('/profile[/{id:[0-9]+}]', 'App\Controllers\UserController:datail')->setName('profile');

    $app->get('/credentials', 'App\Controllers\UserController:credentials')->setName('credentials');
    $app->post('/credentials', 'App\Controllers\UserController:credentialsPost');
})->add($permissions['user']);
