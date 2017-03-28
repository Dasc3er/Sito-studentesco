<?php

$app->group('/quotes', function () use ($app, $permissions) {
    $app->get('', 'App\Controllers\QuoteController:index')->setName('quotes');

    $app->get('/{id:[0-9]}', 'App\Controllers\QuoteController:datail')->setName('quote');

    $app->group('', function () use ($app, $permissions) {
        $app->get('/new', 'App\Controllers\QuoteController:form')->setName('new-quote');
        $app->post('/new', 'App\Controllers\QuoteController:formPost');

        $app->get('/edit/{id:[0-9]}', 'App\Controllers\QuoteController:form')->setName('edit-quote');
        $app->post('/edit/{id:[0-9]}', 'App\Controllers\QuoteController:formPost');

        $app->get('/delete/{id:[0-9]}', 'App\Controllers\QuoteController:delete')->setName('delete-quote');
        $app->post('/delete/{id:[0-9]}', 'App\Controllers\QuoteController:deletePost');
    })->add($permissions['admin']);
})->add($permissions['user']);
