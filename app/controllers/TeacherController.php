<?php

namespace App\Controllers;

use App\Models;

class TeacherController extends \App\Controller
{
    public function index($request, $response, $args)
    {
        $args['results'] = Models\Teacher::orderBy('created_at', 'desc')->paginate(100);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'teachers/index.twig', $args);

        return $response;
    }

    public function datail($request, $response, $args)
    {
        $args['result'] = Models\Teacher::with('quotes')->findOrFail($args['id']);

        $response = $this->view->render($response, 'teachers/datail.twig', $args);

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
        $args['delete'] = true;

        return $this->datail($request, $response, $args);
    }

    public function deletePost($request, $response, $args)
    {
        if (!empty($args['id'])) {
            Models\Teacher::findOrFail($args['id'])->delete();
        }

        $this->router->redirectTo('teachers');

        return $response;
    }
}
