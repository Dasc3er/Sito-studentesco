<?php

namespace App\Core;

use Whoops\Util\Misc;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

class AppContainer
{
    protected static $app = null;
    protected static $settings = null;
    protected static $eloquent = null;

    public static function getInstance()
    {
        if (empty(self::$app)) {
            self::$app = self::makeInstance();
        }

        return self::$app;
    }

    protected static function makeInstance()
    {
        $config['settings'] = self::settings();

        $container = new \Slim\Container($config);

        self::eloquent();
        self::dependecies($container);

        // Monolog
        $logger = new \Monolog\Logger($config['settings']['logger']['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
        $logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__.'/../../'.$config['settings']['logger']['path'].'/info.log', 0, \Monolog\Logger::INFO));
        $logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__.'/../../'.$config['settings']['logger']['path'].'/error.log', 0, \Monolog\Logger::ERROR));
        $logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__.'/../../'.$config['settings']['logger']['path'].'/emergency.log', 0, \Monolog\Logger::EMERGENCY));

        $container['logger'] = $logger;

        if (!empty($config['settings']['displayErrorDetails'])) {
            self::debug($container);
        }
        \Monolog\ErrorHandler::register($container['logger']);

        $app = new \Slim\App($container);

        self::routes($app);
        self::middlewares($app);

        return $app;
    }

    public static function settings()
    {
        if (empty(self::$settings)) {
            $default = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__.'/../../config.default.yml'));

            $settings = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__.'/../../config.yml'));

            $settings = \Utils::array_merge($default, $settings);

            if ($settings['debug']['enable']) {
                $settings['displayErrorDetails'] = true;
            }

            self::$settings = $settings;
        }

        return self::$settings;
    }

    public static function eloquent()
    {
        if (empty(self::$eloquent)) {
            $settings = self::settings();

            $capsule = new \Illuminate\Database\Capsule\Manager();
            foreach ($settings['connections'] as $key => $connection) {
                $capsule->addConnection($connection, $key);
            }

            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            self::$eloquent = $capsule;
        }

        return self::$eloquent;
    }

    protected static function debug(\Slim\Container $container)
    {
        // Debugbar
        $debugbar = new \DebugBar\StandardDebugBar();
        $debugbar->addCollector(new \App\Extensions\EloquentCollector(self::eloquent()));
        $debugbar->addCollector(new \DebugBar\Bridge\MonologCollector($container['logger']));

        $container['debugbar'] = $debugbar;

        // Whoops error handling
        if (!empty($container['settings']['debug']['enableWhoops'])) {
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

    protected static function dependecies(\Slim\Container $container)
    {
        require __DIR__.'/config/dependencies.php';
    }

    protected static function routes(\Slim\App $app)
    {
        $permissions = $app->getContainer()['permissions'];

        $routes = glob(__DIR__.'/../../routes/*.php');
        foreach ($routes as $route) {
            require $route;
        }
    }

    protected static function middlewares(\Slim\App $app)
    {
        $container = $app->getContainer();
        require __DIR__.'/config/middlewares.php';
    }

    public static function container()
    {
        return self::getInstance()->getContainer();
    }
}
