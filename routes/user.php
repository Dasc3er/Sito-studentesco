<?php

$app->group('', function () use ($app, $permissions) {
    $app->get('/profile', 'App\Controllers\UserController:profile')->setName('profile');

     $app->get('/credentials', 'App\Controllers\UserController:credentials')->setName('credentials');
    $app->post('/credentials', 'App\Controllers\UserController:credentialsPost');
})->add($permissions['user']);
