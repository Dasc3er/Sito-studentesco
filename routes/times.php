<?php

$app->group('/times', function () use ($app, $permissions) {
    $app->get('', 'App\Controllers\TimeController:index')->setName('times');

    $app->get('/new', 'App\Controllers\TimeController:form')->setName('new-time');
    $app->post('/new', 'App\Controllers\TimeController:formPost');

    $app->get('/edit/{id:[0-9]}', 'App\Controllers\TimeController:form')->setName('edit-time');
    $app->post('/edit/{id:[0-9]}', 'App\Controllers\TimeController:formPost');

    $app->get('/delete/{id:[0-9]}', 'App\Controllers\TimeController:delete')->setName('delete-time');
    $app->post('/delete/{id:[0-9]}', 'App\Controllers\TimeController:deletePost');

})->add($permissions['admin']);
