<?php

namespace App\Core;

use FastRoute\RouteParser;
use Slim\InvalidArgumentException;
use RuntimeException;

class Router extends \Slim\Router
{
    protected $translator;
    protected $redirectTo;
    protected $redirectParamenters;

    /**
     * Create new router.
     *
     * @param RouteParser $parser
     */
    public function __construct(Translator $translator, \FastRoute\RouteParser $parser = null)
    {
        $this->translator = $translator;
        parent::__construct($parser);
    }

    public function __invoke($request, $response, $next)
    {
        $response = $next($request, $response);

        if (!empty($this->redirectTo)) {
            $response = $this->redirect($response, $this->redirectTo, $this->redirectParamenters);
        }

        return $response;
    }

    /**
     * Create a new Route object.
     *
     * @param string[] $methods Array of HTTP methods
     * @param string   $pattern The route pattern
     * @param callable $handler The route callable
     *
     * @return Slim\Interfaces\RouteInterface
     */
    protected function createRoute($methods, $pattern, $callable)
    {
        $languages = [];
        foreach ($this->translator->getAvailableLocales() as $lang) {
            array_push($languages, '/'.$lang);
        }
        array_push($languages, '');
        $pattern = '{locale:'.implode('|', $languages).'}'.$pattern;

        return parent::createRoute($methods, $pattern, $callable);
    }

    /**
     * Build the path for a named route including the base path.
     *
     * @param string $name        Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
    public function pathFor($name, array $data = [], array $queryParams = [])
    {
        if (!isset($data['locale'])) {
            $data['locale'] = $this->translator->getUrlLocale();
        }

        return parent::pathFor($name, $data, $queryParams);
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

    public function hasRoute($name)
    {
        try {
            return parent::getNamedRoute($name);
        } catch (RuntimeException $e) {
        }
    }
}
