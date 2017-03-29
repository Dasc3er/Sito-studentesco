<?php

namespace App\Controllers;

use App\Models;

class CourseController extends \App\Core\BaseContainer
{
    public function all($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();

            return $container['filter']->page;
        });

        $event = Models\Event::orderBy('date', 'desc')->first();

        if (!empty($event)) {
            $args['results'] = $event->courses()->paginate(10);
            $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

            $args['time'] = $event->date >= \Carbon\Carbon::now();
        }

        $response = $this->view->render($response, 'courses/all.twig', $args);

        return $response;
    }

    public function index($request, $response, $args)
    {
        $event = Models\Event::orderBy('date', 'desc')->first();

        if (!empty($event)) {
            $args['times'] = Models\Time::with('courses')->whereHas('courses', function ($q) use ($event) {
                $q->where(['event_id' => $event->id]);
            })->get();
            $t = \Utils::array_pluck($args['times']->toArray(), 'id');

            $results = [];
            for ($i = 1; $i <= $args['times']->count(); ++$i) {
                $results = array_merge($results, \Utils::combinations($t, $i));
            }

            foreach ($results as $result_key => $result_value) {
                $courses = Models\Course::with('times')->whereHas('times', function ($q) use ($result_value, $event) {
                    $q->whereIn('times.id', $result_value);
                    $q->where(['event_id' => $event->id]);
                })->get();

                foreach ($courses as $key => $course) {
                    $ts = $course->times();
                    $remove = false;
                    if ($ts->count() != count($result_value)) {
                        $remove = true;
                    }

                    foreach ($ts as $t) {
                        if (!in_array($t->id, $result_value)) {
                            $remove = true;
                        }
                    }

                    if ($remove) {
                        unset($courses[$key]);
                    }
                }

                $count = count($courses);

                if (!empty($count)) {
                    $id = [];
                    $name = [];
                    foreach ($result_value as $key => $value) {
                        foreach ($args['times'] as $time) {
                            if ($value == $time->id) {
                                $id[] = $time->id;
                                $name[] = $time->name;
                            }
                        }
                    }

                    $results[$result_key]['id'] = implode(',', $id);
                    $results[$result_key]['name'] = implode(', ', $name);
                    $results[$result_key]['count'] = $count;
                } else {
                    unset($results[$result_key]);
                }
            }

            $args['results'] = $results;
        }

        $response = $this->view->render($response, 'courses/index.twig', $args);

        return $response;
    }

    public function category($request, $response, $args)
    {
        $event = Models\Event::orderBy('date', 'desc')->first();

        if (!empty($event)) {
            $result_value = explode(',', $args['id']);
            $courses = Models\Course::with('times')->whereHas('times', function ($q) use ($result_value, $event) {
                $q->whereIn('times.id', $result_value);
                $q->where(['event_id' => $event->id]);
            })->get();

            foreach ($courses as $key => $course) {
                $ts = $course->times();
                $remove = false;
                if ($ts->count() != count($result_value)) {
                    $remove = true;
                }

                foreach ($ts as $t) {
                    if (!in_array($t->id, $result_value)) {
                        $remove = true;
                    }
                }

                if ($remove) {
                    unset($courses[$key]);
                }
            }

            $args['results'] = $courses;
        }

        $response = $this->view->render($response, 'courses/category.twig', $args);

        return $response;
    }

    public function datail($request, $response, $args)
    {
        $args['result'] = Models\Course::findOrFail($args['id']);

        $args['users'] = $args['result']->users()->get();

        $response = $this->view->render($response, 'courses/datail.twig', $args);

        return $response;
    }

    public function action($request, $response, $args)
    {
        $course = Models\Course::with('users', 'times')->findOrFail($args['id']);

        $event = \App\Models\Event::orderBy('date', 'desc')->first();

        if (!empty($course) && $course->event_id == $event->id) {
            if ($this->auth->user()->isSubscribedTo($course)) {
                $course->users()->detach($this->auth->user());
                $this->flash->addMessage('infos', $this->translator->translate('course.removeSubscription'));
            } else {
                if ($this->auth->user()->isFreeTime($course, $event)) {
                    $course->users()->attach($this->auth->user());
                    $this->flash->addMessage('infos', $this->translator->translate('course.addSubscription'));
                } else {
                    $this->flash->addMessage('errors', $this->translator->translate('course.subscriptionError'));
                }
            }
        }

        $this->router->redirectTo('courses');
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

            $course->school()->associate($school);
            $course->name = $this->filter->name;
            $course->description = $this->filter->description;
            $course->place = $this->filter->place;
            $course->capacity = $this->filter->capacity;

            $team_capacity = $this->filter->team_capacity;
            if (!empty($team_capacity)) {
                $course->team_capacity = $team_capacity;
            }

            $course->save();

            $course->times()->sync($this->filter->times);

            $this->flash->addMessage('infos', $this->translator->translate('course.success'));
            $this->router->redirectTo('courses');
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
            Models\Course::findOrFail($args['id'])->delete();
        }

        $this->router->redirectTo('courses');

        return $response;
    }
}
