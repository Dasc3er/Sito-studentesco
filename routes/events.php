<?php

$app->group('/events', function () use ($app) {
    $app->get('', 'App\Controllers\EventController:index')->setName('events');

    $app->get('/{id:[0-9]+}', 'App\Controllers\EventController:datail')->setName('event');

    $app->get('/new', 'App\Controllers\EventController:form')->setName('new-event');
    $app->post('/new', 'App\Controllers\EventController:formPost');

    $app->get('/edit/{id:[0-9]+}', 'App\Controllers\EventController:form')->setName('edit-event');
    $app->post('/edit/{id:[0-9]+}', 'App\Controllers\EventController:formPost');

    //$app->get('/delete/{id:[0-9]+}', 'App\Controllers\EventController:delete')->setName('delete-event');
    //$app->post('/delete/{id:[0-9]+}', 'App\Controllers\EventController:deletePost');
})->add('App\Middlewares\Authorization\AdminMiddleware');
