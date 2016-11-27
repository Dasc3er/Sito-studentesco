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
        $settings['user'] = (array) $settings['user'];

        $menu = [];
        $menu['left'] = (array) $settings['left'];
        $menu['right'] = (array) $settings['right'];
        $menu['footer'] = (array) $settings['footer'];

        foreach ($menu as $key => $value) {
            if (in_array('auth', $value)) {
                if ($this->auth->check()) {
                    if ($this->auth->admin()) {
                        array_push($menu[$key], 'administration');
                    }

                    array_push($settings['user'], 'logout');
                    $menu[$key]['user'] = $settings['user'];
                } else {
                    array_push($menu[$key], 'login');
                }

                unset($menu[$key][array_search('auth', $value)]);
            }
        }

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
            $result = [];

            if (is_array($value)) {
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
            if(strpos($result['title'], ':username') !== false){
                $result['title'] = $this->translator->translate($title, array(':username' => $this->auth->user()->username));
            }
            $result['path'] = $path;
            $result['state'] = $state;

            array_push($menu, $result);
        }

        return [$menu, $state];
    }
}
