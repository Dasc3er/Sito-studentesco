<?php

$app->group('/phinx', function () use ($app) {
    $app->get('', 'App\Controllers\PhinxController:migrate')->setName('migrate');
});
