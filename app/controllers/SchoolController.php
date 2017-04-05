<?php

namespace App\Controllers;

use App\Models;

class SchoolController extends \App\Controller
{
    public function index($request, $response, $args)
    {
        $args['results'] = Models\School::orderBy('created_at', 'desc')->paginate(10);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'schools/index.twig', $args);

        return $response;
    }

    public function form($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\School::findOrFail($args['id']);
        }

        $response = $this->view->render($response, 'schools/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            if (!empty($args['id'])) {
                $school = Models\School::findOrFail($args['id']);
            } else {
                $school = new Models\School();
            }

            $school->name = $this->filter->name;

            $school->save();

            $this->flash->addMessage('infos', $this->translator->translate('school.success'));
            $this->router->redirectTo('schools');
        }

        return $response;
    }
}
