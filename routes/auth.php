<?php

$app->group('/auth', function () use ($app, $permissions) {
    $app->get('/login', 'App\Controllers\AuthController:login')->setName('login');
    $app->post('/login', 'App\Controllers\AuthController:loginPost');

    $app->get('/register', 'App\Controllers\AuthController:register')->setName('registration');
    $app->post('/register', 'App\Controllers\AuthController:registerPost');

    $app->get('/retrieve', 'App\Controllers\AuthController:retrieve')->setName('recupero-password');
    $app->post('/retrieve', 'App\Controllers\AuthController:retrievePost');

    $app->get('/retrieve/{token}', 'App\Controllers\AuthController:retrieveToken')->setName('recupero');
    $app->post('/retrieve/{token}', 'App\Controllers\AuthController:retrieveTokenPost');
})->add($permissions['guest']);

$app->get('/auth/logout', 'App\Controllers\AuthController:logout')->setName('logout')->add($permissions['user']);

$app->get('/verify/{code}', 'App\Controllers\UserController:verifyEmail')->setName('verifica-email');

$app->get('/send-verification-code', 'App\Controllers\UserController:sendVerify')->setName('invia-verifica')->add($permissions['user']);