<?php

namespace App\Controllers;

use App\Models;

class TeacherController extends \App\Core\BaseContainer
{
    public function index($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();
            return $container['filter']->page;;
        });

        $args['results'] = Models\Teacher::orderBy('created_at', 'desc')->paginate(10);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'teachers/index.twig', $args);

        return $response;
    }

    public function form($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\Teacher::findOrFail($args['id']);
        }

        $response = $this->view->render($response, 'teachers/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            if (!empty($args['id'])) {
                $teacher = Models\Teacher::findOrFail($args['id']);
            } else {
                $teacher = new Models\Teacher();
            }

            $teacher->name = $this->filter->name;

            $teacher->save();

            $this->flash->addMessage('infos', $this->translator->translate('teacher.success'));
            $this->router->redirectTo('teachers');
        }

        return $response;
    }

    public function delete($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\Teacher::findOrFail($args['id']);
        }

        $response = $this->view->render($response, 'teachers/form.twig', $args);

        return $response;
    }

    public function deletePost($request, $response, $args)
    {
        $response = $this->view->render($response, 'teachers/form.twig', $args);

        return $response;
    }
}
