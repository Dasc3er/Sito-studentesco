<?php

namespace App\Controllers;

use App\Models;

class TimeController extends \App\Core\BaseContainer
{
    public function index($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();
            return $container['filter']->page;;
        });

        $args['results'] = Models\Time::orderBy('created_at', 'desc')->paginate(10);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'times/index.twig', $args);

        return $response;
    }

    public function form($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\Time::findOrFail($args['id']);
        }

        $response = $this->view->render($response, 'times/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            if (!empty($args['id'])) {
                $time = Models\Time::findOrFail($args['id']);
            } else {
                $time = new Models\Time();
            }

            $time->name = $this->filter->name;

            $time->save();

            $this->flash->addMessage('infos', $this->translator->translate('time.success'));
            $this->router->redirectTo('times');
        }

        return $response;
    }

    public function delete($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\Time::findOrFail($args['id']);
        }

        $response = $this->view->render($response, 'times/form.twig', $args);

        return $response;
    }

    public function deletePost($request, $response, $args)
    {
        $response = $this->view->render($response, 'times/form.twig', $args);

        return $response;
    }
}
