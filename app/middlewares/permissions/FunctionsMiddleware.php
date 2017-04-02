<?php

namespace App\Middlewares\Permissions;

class FunctionsMiddleware extends PermissionMiddleware
{
    protected $routeName;

    public function __invoke($request, $response, $next)
    {
        $route = $request->getAttribute('route');
        $routeName = null;
        if (isset($route)) {
            $this->routeName = $route->getName();
        }

        return parent::__invoke($request, $response, $next);
    }

    protected function operation($request, $response)
    {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    protected function hasPermission()
    {
        return !(isset($this->settings['app']['functions'][$this->routeName]) && empty($this->settings['app']['functions'][$this->routeName]));
    }
}
