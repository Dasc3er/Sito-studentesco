<?php

use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Asset\PathPackage;

use Respect\Validation\Validator as v;


// Auth manager
$container['auth'] = function ($container) {
    return new \App\Auth($container);
};

// Language manager
$container['translator'] = function ($container) {
    return new \App\Translator($container->settings['lang']);
};

// Custom router
$container['router'] = function ($container) {
    return new \App\Router($container);
};

// Flash messages
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// CSRF protection
$container['csrf'] = function ($container) {
    $csrf = new \Slim\Csrf\Guard();
    $csrf->setPersistentTokenMode(true);

    return $csrf;
};

// Render
$container['view'] = function ($container) {
    $settings = $container->settings['views'];

    $view = new \Slim\Views\Twig($settings['templates'], $settings['config']);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new \Slim\Views\TwigExtension($container['router'], $basePath));
    $view->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension($container['translator']->getTranslator()));
    $view->addExtension(new \App\Extensions\TwigCsrfExtension($container['csrf']));

    $assets = $basePath.'/'.$settings['assets'];
    $view->offsetSet('css', new PathPackage($assets.'/css', new EmptyVersionStrategy()));
    $view->offsetSet('js', new PathPackage($assets.'/js', new EmptyVersionStrategy()));
    $view->offsetSet('img', new PathPackage($assets.'/img', new EmptyVersionStrategy()));

    if(!empty($container['debugbar'])){
        $debugbar = $container['debugbar']->getJavascriptRenderer();
        $debugbar->setBaseUrl($assets.'/php-debugbar');
        $view->offsetSet('debugbar', $debugbar);
    }

    $view->offsetSet('functions', $container->settings['app']['functions']);
    $view->offsetSet('superuser', $container->settings['app']['superuser']);

    $view->offsetSet('auth', $container['auth']);
    $view->offsetSet('flash', $container['flash']);
    $view->offsetSet('translator', $container['translator']);
    $view->offsetSet('router', $container['router']);

    return $view;
};

// Sanitizing methods
$container['filter'] = function ($container) {
    return new \App\Middlewares\FilterMiddleware($container);
};

// Register provider
$container['validator'] = function () {
    //Create the validators
    $validators = [
        'username' => v::alnum()->noWhitespace()->length(1, 10),
        'email' => v::email(),
        'password' => v::alnum()->noWhitespace()->length(1, 55),
        'age' => v::numeric()->positive()->between(1, 100),
    ];

    return new \App\Middlewares\ValidationMiddleware($validators);
};

// Exception handlers
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $url = explode('/', $request->getAttribute('routeInfo')['request'][1]);
        if (isset($url[4]) && in_array($url[4], $container['translator']->getAvailableLocales())) {
            $container['translator']->setLocale($url[4]);
        }

        return $container['view']->render($response, 'errors/404.twig');
    };
};

$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $url = explode('/', $request->getAttribute('routeInfo')['request'][1]);
        if (isset($url[4]) && in_array($url[4], $container['translator']->getAvailableLocales())) {
            $container['translator']->setLocale($url[4]);
        }

        return $container['view']->render($response, 'errors/405.twig');
    };
};

if (empty($container->settings['displayErrorDetails'])) {
    $container['errorHandler'] = function ($container) {
        return function ($request, $response, $exception) use ($container) {
            $url = explode('/', $request->getAttribute('routeInfo')['request'][1]);
            if (isset($url[4]) && in_array($url[4], $container['translator']->getAvailableLocales())) {
                $container['translator']->setLocale($url[4]);
            }

            return $container['view']->render($response, 'errors/500.twig');
        };
    };
}
