<?php

namespace App\Controllers;

use App\Models;

class AdminController extends \App\Core\BaseContainer
{
    public function index($request, $response, $args)
    {
        $response = $this->view->render($response, 'admin/administration.twig', $args);

        return $response;
    }

    public function logins($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();
            return $container['filter']->page;;
        });

        $args['results'] = Models\Login::with('user')->orderBy('created_at', 'desc')->paginate(100);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $args['count'] = $args['results']->count();

        $response = $this->view->render($response, 'admin/logins.twig', $args);

        return $response;
    }

    public function resetlogins($request, $response, $args)
    {
        Models\Login::truncate();
        $this->router->redirectTo('visite');

        return $response;
    }

    public function visits($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();
            return $container['filter']->page;;
        });

        $args['results'] = Models\Visit::orderBy('created_at', 'desc')->paginate(100);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $args['count'] = $args['results']->count();

        $response = $this->view->render($response, 'admin/visits.twig', $args);

        return $response;
    }

    public function resetVisits($request, $response, $args)
    {
        Models\Visit::truncate();
        $this->router->redirectTo('visite');

        return $response;
    }
}
