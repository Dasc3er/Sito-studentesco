<?php

$app->group('/schools', function () use ($app, $permissions) {
    $app->get('', 'App\Controllers\SchoolController:index')->setName('schools');

    $app->get('/new', 'App\Controllers\SchoolController:form')->setName('new-school');
    $app->post('/new', 'App\Controllers\SchoolController:formPost');

    $app->get('/edit/{id:[0-9]}', 'App\Controllers\SchoolController:form')->setName('edit-school');
    $app->post('/edit/{id:[0-9]}', 'App\Controllers\SchoolController:formPost');

    $app->get('/delete/{id:[0-9]}', 'App\Controllers\SchoolController:delete')->setName('delete-school');
    $app->post('/delete/{id:[0-9]}', 'App\Controllers\SchoolController:deletePost');

})->add($permissions['admin']);
