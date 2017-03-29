<?php

namespace App\Controllers;

use App\Models;

class AdminController extends \App\Core\BaseContainer
{
    public function index($request, $response, $args)
    {
        $response = $this->view->render($response, 'admin/administration.twig', $args);

        return $response;
    }

    public function upload($request, $response, $args)
    {
        $response = $this->view->render($response, 'admin/upload.twig', $args);

        return $response;
    }

    public function uploadPost($request, $response, $args)
    {
        set_time_limit(0);

        if (!empty($_FILES['file']['tmp_name'])) {
            $targetFile = dirname(__FILE__).DIRECTORY_SEPARATOR.'text.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);

            $datas = file($targetFile);
            unlink($targetFile);

            $users = Models\User::has('group')->get();
            foreach ($users as $user) {
                $user->group()->dissociate();
            }

            foreach ($datas as $data) {
                $data = explode(';', $data);

                if (count($data) == 4 || count($data) == 3) {
                    if (count($data) == 4) {
                        $school_name = $data[3];
                        $group_name = $data[2];
                        $name = $data[1];
                        $number = $data[0];
                    } else {
                        $school_name = $data[2];
                        $group_name = $data[1];
                        $name = $data[0];
                    }

                    $school_name = ucfirst($school_name);
                    $group_name = strtoupper($group_name);
                    $name = ucwords($name);

                    $school = Models\School::where(['name' => $school_name])->first();
                    if (empty($school)) {
                        $school = new Models\School();
                        $school->name = $school_name;
                        $school->save();
                    }

                    $group = Models\Group::where(['name' => $group_name, 'school_id' => $school->id])->first();
                    if (empty($group)) {
                        $group = new Models\Group();
                        $group->name = $group_name;
                        $group->school()->associate($school);
                        $group->save();
                    }

                    $user = null;
                    if (!empty($number)) {
                        $user = Models\User::where(['number' => $school->id.'_'.$number])->first();
                    }
                    if (empty($user)) {
                        $user = new Models\User();
                        $user->name = $name;
                        if (!empty($number)) {
                            $user->number = $school->id.'_'.$number;
                        }
                        $user->role = 0;

                        $replace = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                        $to = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
                        $username = substr(str_replace(' ', '.', str_replace($replace, $to, $name)), 0, 20);
                        while (!\Utils::isUsernameFree($username, false)) {
                            $username .= rand(0, 999);
                        }
                        $user->username = $username;

                        $length = 5;
                        $password = '';
                        while (strlen($password) <= $length) {
                            $what = rand(0, 1);
                            if ($what == 0) {
                                $password .= rand(0, 99);
                            } else {
                                $password .= chr(rand(97, 122));
                            }
                        }
                        $user->password = $password;

                        $user->group()->associate($group);
                        $user->save();
                    }
                }
            }
        }

        $this->router->redirectTo('users');

        return $response;
    }

    public function logins($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();

            return $container['filter']->page;
        });

        $args['results'] = Models\Login::with('user')->orderBy('created_at', 'desc')->paginate(100);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'admin/logins.twig', $args);

        return $response;
    }

    public function resetlogins($request, $response, $args)
    {
        Models\Login::truncate();
        $this->router->redirectTo('visite');

        return $response;
    }

    public function visits($request, $response, $args)
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function () {
            $container = \App\Core\AppContainer::container();

            return $container['filter']->page;
        });

        $args['results'] = Models\Visit::orderBy('created_at', 'desc')->paginate(100);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'admin/visits.twig', $args);

        return $response;
    }

    public function resetVisits($request, $response, $args)
    {
        Models\Visit::truncate();
        $this->router->redirectTo('visite');

        return $response;
    }
}
