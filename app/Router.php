<?php

namespace App;

use RuntimeException;

class Router extends \Slim\Router
{
    protected $container;

    public function __construct($container, \FastRoute\RouteParser $parser = null)
    {
        parent::__construct($parser);

        $this->container = $container;
    }

    protected function createRoute($methods, $pattern, $callable)
    {
        if ($this->container->settings['lang']['routing']) {
            $languages = [];
            foreach ($this->container->translator->getAvailableLocales() as $lang) {
                $languages[] = '/'.$lang;
            }
            $languages[] = '';
            $pattern = '{locale:'.implode('|', $languages).'}'.$pattern;
        }

        return parent::createRoute($methods, $pattern, $callable);
    }

    public function pathFor($name, array $data = [], array $queryParams = [])
    {
        if (!isset($data['locale']) && $this->container->settings['lang']['routing']) {
            $data['locale'] = $this->container->translator->getCurrentLocale();
        }

        return parent::pathFor($name, $data, $queryParams);
    }

    public function hasRoute($name)
    {
        try {
            return parent::getNamedRoute($name);
        } catch (RuntimeException $e) {
            return false;
        }
    }


    protected $redirectTo;
    protected $redirectParamenters;

    public function __invoke($request, $response, $next)
    {
        $response = $next($request, $response);

        if (!empty($this->redirectTo)) {
            $response = $this->redirect($response, $this->redirectTo, $this->redirectParamenters);
        }

        return $response;
    }

    public function redirectTo($redirectTo = 'index', array $redirectParamenters = [])
    {
        $this->redirectTo = $redirectTo;
        $this->redirectParamenters = $redirectParamenters;
    }

    public function redirect($response, $pathName = 'index', array $param = [])
    {
        return $response->withStatus(302)->withHeader('Location', $this->pathFor($pathName, $param));
    }
}
