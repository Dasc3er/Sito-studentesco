<?php

namespace App\Middlewares;

class DataMiddleware extends \App\Core\BaseContainer
{
    protected $routeName;

    public function __invoke($request, $response, $next)
    {
        if (!empty($this->settings['app']['maintenance'])) {
            $this->flash->addMessage('warnings', $this->translator->translate('base.maintenance'));
        }

        $settings = $this->settings['app']['menu'];

        if ($this->auth->admin()) {
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

            $title = $element;
            if ($this->router->hasRoute($element)) {
                $path = $this->router->pathFor($element);
                $title = $title.'.title';
            } else {
                $path = '#';
                $title = 'menu.'.$title;
            }

            $result['title'] = $this->translator->translate($title);
            if (strpos($result['title'], ':username') !== false) {
                $result['title'] = $this->translator->translate($title, [':username' => $this->auth->user()->username]);
            }
            $result['path'] = $path;
            $result['state'] = $state;

            array_push($menu, $result);
        }

        return [$menu, $state];
    }
}
