<?php

namespace App\Controllers;

use App\Models;

class CourseController extends \App\Controller
{
    public function all($request, $response, $args)
    {
        $event = Models\Event::orderBy('date', 'desc')->first();

        if (!empty($event)) {
            $args['results'] = $event->courses()->paginate(10);
            $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

            $args['time'] = $event->subscription_end >= \Carbon\Carbon::now();
        }
        elseif($this->auth->isAdmin()){
            $this->flash->addMessage('errors', $this->translator->translate('course.needs-event'));
            $this->router->redirectTo('new-event');
        }

        $response = $this->view->render($response, 'courses/all.twig', $args);

        return $response;
    }

    public function index($request, $response, $args)
    {
        $event = Models\Event::orderBy('date', 'desc')->first();
        $args['time'] = $event->subscription_end >= \Carbon\Carbon::now();

        if (!empty($event)) {
            $args['times'] = Models\Time::with('courses')->whereHas('courses', function ($q) use ($event) {
                $q->where(['event_id' => $event->id]);
            })->get();
            $t = array_pluck($args['times']->toArray(), 'id');

            $results = [];
            for ($i = 1; $i <= $args['times']->count(); ++$i) {
                $results = array_merge($results, self::combinations($t, $i));
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

					$ts = $ts->get();
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
        elseif($this->auth->isAdmin()){
            $this->flash->addMessage('errors', $this->translator->translate('course.needs-event'));
            $this->router->redirectTo('new-event');
        }

        $response = $this->view->render($response, 'courses/index.twig', $args);

        return $response;
    }

    protected static function combinations(array $array, $choose)
    {
        $result = [];
        $combination = [];

        $n = count($array);

        self::inner(0, $choose, $array, $n, $result, $combination);

        return $result;
    }

    protected static function inner($start, $choose, $array, $n, &$result, &$combination)
    {
        if ($choose == 0) {
            $result[] = $combination;
        } else {
            for ($i = $start; $i <= $n - $choose; ++$i) {
                $combination[] = $array[$i];
                self::inner($i + 1, $choose - 1, $array, $n, $result, $combination);
                array_pop($combination);
            }
        }
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
            $args['time'] = $event->subscription_end >= \Carbon\Carbon::now();
        }

        $response = $this->view->render($response, 'courses/category.twig', $args);

        return $response;
    }

    public function datail($request, $response, $args)
    {
        $event = Models\Event::orderBy('date', 'desc')->first();
        if(empty($event)){
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        $args['time'] = $event->subscription_end >= \Carbon\Carbon::now();

        $args['result'] = Models\Course::with('times', 'users')->find($args['id']);
        if(empty($args['result'])){
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        $args['users'] = $args['result']->users()->with('group')->orderBy('name', 'asc')->get();

        $response = $this->view->render($response, 'courses/datail.twig', $args);

        return $response;
    }

    public function action($request, $response, $args)
    {
        $event = \App\Models\Event::orderBy('date', 'desc')->first();

        $course = Models\Course::with('users', 'times')->find($args['id']);

        if (!empty($event) && !empty($course) && ($course->event_id == $event->id) && ($event->subscription_end >= \Carbon\Carbon::now())) {
            if ($this->auth->getUser()->isSubscribedTo($course)) {
                $course->users()->detach($this->auth->getUser());
                $this->flash->addMessage('infos', $this->translator->translate('course.removeSubscription'));
            } else {
                if ($this->auth->getUser()->isFreeTime($course, $event)) {
                    $course->users()->attach($this->auth->getUser());
                    $this->flash->addMessage('infos', $this->translator->translate('course.addSubscription'));
                } else {
                    $this->flash->addMessage('errors', $this->translator->translate('course.subscriptionError'));
                }
            }
        }

        $this->router->redirectTo('course', ['id' => $args['id']]);
    }

    public function form($request, $response, $args)
    {
        $event = Models\Event::orderBy('date', 'desc')->first();

        if (empty($event) || $event->subscription_end < \Carbon\Carbon::now()) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        if (!empty($args['id'])) {
            $args['result'] = Models\Course::where(['id' => $args['id']])->whereHas('event', function($query) use ($event) {
                $query->where(['id' => $event->id]);
            })->first();

            if (empty($args['result'])) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }
        }

        $args['schools'] = Models\School::all();
        $args['times'] = Models\Time::all();

        $response = $this->view->render($response, 'courses/form.twig', $args);

        return $response;
    }

    public function formPost($request, $response, $args)
    {
        $event = Models\Event::orderBy('date', 'desc')->first();

        if (empty($event) || $event->subscription_end < \Carbon\Carbon::now()) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        if (!$this->validator->hasErrors()) {
            if (!empty($args['id'])) {
                $course = Models\Course::find($args['id']);

                if (empty($course) || $course->event_id != $event->id) {
                    throw new \Slim\Exception\NotFoundException($request, $response);
                }
            } else {
                $course = new Models\Course();
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
            $course = Models\Course::find($args['id']);

            if (empty($course)) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }

            $course->delete();
        }

        $this->router->redirectTo('courses');

        return $response;
    }
}
