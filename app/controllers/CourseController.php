<?php

namespace App\Controllers;

use App\Models;

class CourseController extends \App\Core\BaseContainer
{
    public function index($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();
            return $container['filter']->page;;
        });

        $event = Models\Event::orderBy('date', 'desc')->first();

        $args['results'] = $event->courses()->paginate(10);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $args['time'] = $event->date >= \Carbon\Carbon::now();

        $response = $this->view->render($response, 'courses/index.twig', $args);

        return $response;
    }

    public function datail($request, $response, $args)
    {
        $args['result'] = Models\Course::findOrFail($args['id']);

        $response = $this->view->render($response, 'courses/datail.twig', $args);

        return $response;
    }

    public function form($request, $response, $args)
    {
        if (!Models\Event::where('date', '>=', \Carbon\Carbon::now())->count()) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        if (!empty($args['id'])) {
            $args['result'] = Models\Course::findOrFail($args['id']);
        }

        $args['schools'] = Models\School::all();
        $args['times'] = Models\Time::all();

        $response = $this->view->render($response, 'courses/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors() && Models\Event::where('date', '>=', \Carbon\Carbon::now())->count()) {
            if (!empty($args['id'])) {
                $course = Models\Course::findOrFail($args['id']);
            } else {
                $course = new Models\Course();

                $event = Models\Event::where('date', '>=', \Carbon\Carbon::now())->first();
                $course->event()->associate($event);
            }

            $school = Models\School::findOrFail($this->filter->school);

            $course->times()->sync($this->filter->times);
            $course->school()->associate($school);
            $course->name = $this->filter->name;
            $course->description = $this->filter->description;
            $course->place = $this->filter->place;
            $course->capacity = $this->filter->capacity;
            $course->team_capacity = $this->filter->team_capacity;

            $course->save();

            $this->flash->addMessage('infos', $this->translator->translate('course.success'));
            $this->router->redirectTo('courses');
        }

        return $response;
    }

    public function delete($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\Course::findOrFail($args['id']);
        }

        $response = $this->view->render($response, 'courses/form.twig', $args);

        return $response;
    }

    public function deletePost($request, $response, $args)
    {
        $response = $this->view->render($response, 'courses/form.twig', $args);

        return $response;
    }
}
