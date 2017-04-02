<?php

// Impostazioni per la corretta interpretazione di UTF-8
header('Content-Type: text/html; charset=UTF-8');

$handler = null;
if(extension_loaded('mbstring')){
	mb_internal_encoding('UTF-8');
	mb_http_output('UTF-8');
	mb_http_input('UTF-8');
	mb_language('uni');
	mb_regex_encoding('UTF-8');
	$handler = 'mb_output_handler';
}
ob_start($handler);

// Istanziamento della sessione
session_cache_limiter(false);
session_start();

// Librerie dell'applicazione
require_once __DIR__.'/vendor/autoload.php';

// Slim application
$settings = \App\App::getSettings();

if (!empty($settings['app']['redirectHTTPS']) && !isHTTPS()) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit();
}

$container = new \Slim\Container([
    'settings' => $settings
]);

// Istanziamento di Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager();
foreach ($settings['connections'] as $key => $connection) {
    $capsule->addConnection($connection, $key);
}
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['database'] = $capsule;

// Istanziamento delle dipendenze
require __DIR__.'/app/config/dependencies.php';

// Istanziamento di Monolog
use \Monolog\Logger;
use \Monolog\Handler\RotatingFileHandler;

$logger = new Logger($settings['logger']['name']);
$logger->pushProcessor(new \Monolog\Processor\UidProcessor());
$logger->pushProcessor(new \Monolog\Processor\WebProcessor());
$logger->pushHandler(new RotatingFileHandler(__DIR__.'/'.$settings['logger']['path'].'/info.log', 0, Logger::INFO));
$logger->pushHandler(new RotatingFileHandler(__DIR__.'/'.$settings['logger']['path'].'/error.log', 0, Logger::ERROR));
$logger->pushHandler(new RotatingFileHandler(__DIR__.'/'.$settings['logger']['path'].'/emergency.log', 0, Logger::EMERGENCY));

$container['logger'] = $logger;

// Istanziamento dei sistemi di debug
use Whoops\Util\Misc;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

if (!empty($settings['displayErrorDetails'])) {
    // Debugbar
    $debugbar = new \DebugBar\StandardDebugBar();
    $debugbar->addCollector(new \App\Extensions\EloquentCollector($capsule));
    $debugbar->addCollector(new \DebugBar\Bridge\MonologCollector($container['logger']));

    $container['debugbar'] = $debugbar;

    // Whoops
    if (!empty($settings['debug']['enableWhoops'])) {
        $prettyPageHandler = new PrettyPageHandler();
        $prettyPageHandler->addDataTable('Whoops Default', [
            'Script Name' => $_SERVER['SCRIPT_NAME'],
            'Request URI' => $_SERVER['REQUEST_URI'] ?: '-',
        ]);

        // Set Whoops to default exception handler
        $whoops = new \Whoops\Run();
        $whoops->pushHandler($prettyPageHandler);

        // Enable JsonResponseHandler when request is AJAX
        if (Misc::isAjaxRequest()) {
            $whoops->pushHandler(new JsonResponseHandler());
        }

        $whoops->register();

        // Override the default Slim error handler
        $container['errorHandler'] = function () use ($whoops) {
            return new \App\Extensions\WhoopsErrorHandler($whoops);
        };
    }
}

\Monolog\ErrorHandler::register($container['logger']);

// Istanziamento dell'applicazione Slim
$app = new \Slim\App($container);
\App\App::setApp($app);

// Aggiunta dei percorsi
$routes = glob(__DIR__.'/routes/*.php');
foreach ($routes as $route) {
    require $route;
}

// Aggiunta dei middleware
require __DIR__.'/app/config/middlewares.php';

$app->run();
