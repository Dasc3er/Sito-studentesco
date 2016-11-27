<?php

$app->get('/', 'App\Controllers\BaseController:index')->setName('index');

$app->get('/contacts', 'App\Controllers\BaseController:contacts')->setName('contacts');
