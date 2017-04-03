<?php

namespace App\Controllers;

use App\Models;

class TimeController extends \App\Controller
{
    public function index($request, $response, $args)
    {
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
        $args['delete'] = true;

        return $this->index($request, $response, $args);
    }

    public function deletePost($request, $response, $args)
    {
        if (!empty($args['id'])) {
            Models\Time::findOrFail($args['id'])->delete();
        }

        $this->router->redirectTo('times');

        return $response;
    }
}
