<?php

$app->group('/phinx', function () use ($app, $permissions) {
    $app->get('', 'App\Controllers\PhinxController:migrate')->setName('migrate');
});
