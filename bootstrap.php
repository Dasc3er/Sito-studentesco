<?php

header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');

session_cache_limiter(false);
session_start();

// Librerie dell'applicazione
require_once __DIR__.'/vendor/autoload.php';

// Slim application
$app = \App\Core\AppContainer::getInstance();

$app->run();
