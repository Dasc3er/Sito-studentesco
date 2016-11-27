<?php

session_cache_limiter(false);
session_start();

// Librerie dell'applicazione
require_once __DIR__.'/vendor/autoload.php';

// Slim application
$app = \App\Core\AppContainer::getInstance();

$app->run();
