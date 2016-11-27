<?php

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->add(new \App\Middlewares\DataMiddleware($container));

$app->add($container['validator']);

$app->add($container['filter']);

$app->add($container['router']);

if (isset($container['settings']['lang']['routing']) && $container['settings']['lang']['routing']) {
    $app->add($container['translator']);
}

// CSRF
$app->add($container['csrf']);

// Trailing slash
$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // Permanently redirect paths with a trailing slash to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));

        if ($request->getMethod() == 'GET') {
            return $response->withRedirect((string) $uri, 301);
        } else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

// Whoops error handling
if (!empty($container['settings']['debug']['enableWhoops'])) {
    $app->add(new \Dasc3er\Slim\Whoops\WhoopsMiddleware($container, $container['settings']['whoopsEditor']));
}

$app->add(new \App\Middlewares\Permissions\FunctionsMiddleware($container));

