<?php

namespace App\Controllers;

use App\Models;

class EventController extends \App\Controller
{
    public function index($request, $response, $args)
    {
        $args['results'] = Models\Event::orderBy('created_at', 'desc')->paginate(10);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'events/index.twig', $args);

        return $response;
    }

    public function form($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\Event::findOrFail($args['id']);
        }

        $response = $this->view->render($response, 'events/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            if (!empty($args['id'])) {
                $event = Models\Event::findOrFail($args['id']);
            } else {
                $event = new Models\Event();
            }

            $event->name = $this->filter->name;
            $event->date = $this->filter->date;
            $event->subscription_end = $this->filter->subscription_end;

            $event->save();

            $this->flash->addMessage('infos', $this->translator->translate('event.success'));
            $this->router->redirectTo('events');
        }

        return $response;
    }

    public function delete($request, $response, $args)
    {
        $args['delete'] = true;

        return $this->datail($request, $response, $args);
    }

    public function deletePost($request, $response, $args)
    {
        if (!empty($args['id'])) {
            Models\Event::findOrFail($args['id'])->delete();
        }

        $this->router->redirectTo('events');

        return $response;
    }
}
