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
        $teacher = Models\Teacher::with('quotes')->find($args['id']);
        if (empty($teacher)) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        $args['result'] = $teacher;

        $response = $this->view->render($response, 'teachers/datail.twig', $args);

        return $response;
    }

    public function form($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $teacher = Models\Teacher::find($args['id']);

            if (empty($teacher) || ($teacher->user_id != $this->auth->getUser()->id && !$this->auth->isAdmin())) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }

            $args['result'] = $teacher;
        }

        $response = $this->view->render($response, 'teachers/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            if (!empty($args['id'])) {
                $teacher = Models\Teacher::find($args['id']);

                if (empty($teacher) || ($teacher->user_id != $this->auth->getUser()->id && !$this->auth->isAdmin())) {
                    throw new \Slim\Exception\NotFoundException($request, $response);
                }
            } else {
                $teacher = new Models\Teacher();
            }

            $teacher->name = $this->filter->name;
            $teacher->user()->associate($this->auth->getUser());

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
            $teacher = Models\Teacher::find($args['id']);

            if (empty($teacher) || ($teacher->user_id != $this->auth->getUser()->id && !$this->auth->isAdmin())) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }

            $teacher->delete();
        }

        $this->router->redirectTo('teachers');

        return $response;
    }
}
