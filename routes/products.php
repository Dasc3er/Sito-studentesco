<?php

$app->group('/products', function () use ($app, $permissions) {
    $app->get('', 'App\Controllers\TestController:index')->setName('prodotti');

    $app->get('/{id:[0-9]}', 'App\Controllers\TestController:product')->setName('prodotto');

    $app->get('/new', 'App\Controllers\TestController:createProduct')->setName('nuovo-prodotto');
    $app->post('/new', 'App\Controllers\TestController:productPost');

    $app->get('/edit/{id:[0-9]}', 'App\Controllers\TestController:editProduct')->setName('modifica-prodotto');
    $app->post('/edit/{id:[0-9]}', 'App\Controllers\TestController:productPost');

    $app->get('/upload/{id:[0-9]}', 'App\Controllers\TestController:upload');
});

$app->post('/save-upload', 'App\Controllers\TestController:saveUpload')->setName('salva-upload');
