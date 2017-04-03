<?php

namespace App\Middlewares;

class DataMiddleware extends \App\Middleware
{
    protected $routeName;

    public function __invoke($request, $response, $next)
    {
        if (!empty($this->settings['app']['maintenance'])) {
            $this->flash->addMessage('warnings', $this->translator->translate('base.maintenance'));
        }

        $settings = $this->settings['app']['menu'];

        if ($this->auth->isAdmin()) {
            $menus = $settings['admin'];
        } elseif ($this->auth->check()) {
            $menus = $settings['user'];
        } else {
            $menus = $settings['guest'];
        }

        $menu = [];
        $menu['left'] = (array) $menus['left'];
        $menu['right'] = (array) $menus['right'];
        $menu['footer'] = (array) $menus['footer'];

        // Creazione dei link di navigazione
        $route = $request->getAttribute('route');
        $routeName = null;
        if (isset($route)) {
            $this->routeName = $route->getName();
        }

        $menu['left'] = $this->menu($menu['left'])[0];
        $menu['right'] = $this->menu($menu['right'])[0];
        $menu['footer'] = $this->menu($menu['footer'])[0];

        $this->view->offsetSet('menu', $menu);

        $response = $next($request, $response);

        return $response;
    }

    protected function menu($list, $state = false)
    {
        $menu = [];

        foreach ($list as $key => $value) {
            $state = false;
            $result = [];

            if (is_array($value)) {
                $key = key($value);
                $value = $value[$key];

                $submenu = $this->menu($value, $state);
                $result['children'] = $submenu[0];

                $element = $key;
            } else {
                $element = $value;
            }

            if ($this->routeName == $element || !empty($submenu[1])) {
                $state = true;
            }

            if ($this->router->hasRoute($element)) {
                $path = $this->router->pathFor($element);
                $title = $element.'.title';
            } elseif (starts_with(strtolower($element), 'http') || starts_with(strtolower($element), 'https')) {
                $results = explode(' ', $element, 2);
                $path = $results[0];
                $title = $results[1];
            } else {
                $path = '#';
                $title = 'menu.'.$element;
            }

            $replace = [];
            if ($this->auth->check()) {
                $replace[':username'] = $this->auth->getUser()->username;
            }

            $result['title'] = $this->translator->translate($title, $replace);
            $result['path'] = $path;
            $result['state'] = $state;

            array_push($menu, $result);
        }

        return [$menu, $state];
    }
}
