<?php

namespace App\Controllers;

use App\Models;

class UserController extends \App\Controller
{
    public function index($request, $response, $args)
    {
        $args['results'] = Models\User::orderBy('name', 'asc')->withTrashed()->paginate(30);
        $args['results']->setPath($this->router->pathFor($request->getAttribute('route')->getName()));

        $response = $this->view->render($response, 'users/index.twig', $args);

        return $response;
    }

    public function datail($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $args['result'] = Models\User::findOrFail($args['id']);
        } elseif ($this->auth->check()) {
            $args['result'] = $this->auth->getUser();
        }

        $response = $this->view->render($response, 'users/detail.twig', $args);

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
            $user = Models\User::findOrFail($args['id']);

            if ($user->id != $this->auth->getUser()->id && $user->id != $this->settings['app']['superuser']) {
                $user->cascadeDelete();
            }
        }

        $this->router->redirectTo('users');

        return $response;
    }

    public function enable($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $user = Models\User::withTrashed()->findOrFail($args['id']);

            if ($user->id != $this->auth->getUser()->id) {
                $user->restore();
            }
        }

        $this->router->redirectTo('users');

        return $response;
    }

    public function admin($request, $response, $args)
    {
        if (!empty($args['id'])) {
            $user = Models\User::withTrashed()->findOrFail($args['id']);

            if ($user->role == 0) {
                $user->role = 1;
            } else {
                $user->role = 0;
            }

            $user->save();
        }

        $this->router->redirectTo('users');

        return $response;
    }

    public function credentials($request, $response, $args)
    {
        $args['result'] = $this->auth->getUser()->toArray();

        $response = $this->view->render($response, 'users/credentials.twig', $args);

        return $response;
    }

    public function credentialsPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            $name = $this->filter->name;
            $username = $this->filter->username;
            $email = $this->filter->email;
            $password = $this->filter->password;
            $rep_password = $this->filter->rep_password;

            $userFree = Models\User::isUsernameFree($username);
            $emailFree = Models\User::isEmailFree($email);

            if ($userFree && $emailFree && $password == $rep_password) {
                $user = $this->auth->getUser();

                $user->name = $name;
                $user->username = $username;
                $user->password = $password;
                $user->email_token = secure_random_string();

                if ($user->email != $email) {
                    $user->email = $email;
                    \Utils::sendEmail($email, 'verification', [':path' => 'http://'.$request->getUri()->getHost().$this->router->pathFor('verify-email', ['code' => $user->email_token])]);
                }

                $user->save();

                $this->flash->addMessage('infos', $this->translator->translate('register.success'));
                $this->router->redirectTo();

                session_regenerate_id();
            } else {
                if ($password != $rep_password) {
                    $this->flash->addMessage('errors', $this->translator->translate('base.errorPassword'));
                }
                if ($userFree) {
                    $this->flash->addMessage('errors', $this->translator->translate('base.errorEmail'));
                }
                if ($emailFree) {
                    $this->flash->addMessage('errors', $this->translator->translate('base.errorUsername'));
                }

                $this->router->redirectTo('credentials');
            }
        }

        return $response;
    }

    public function verifyEmail($request, $response, $args)
    {
        Models\User::where(['email_token' => $args['code']])->update(['email_token' => null]);
        $this->router->redirectTo();

        return $response;
    }

    public function sendVerify($request, $response, $args)
    {
        $user = $this->auth->getUser();

        $user->email_token = secure_random_string();
        $user->save();

        \Utils::sendEmail($user->email, 'verification', [':path' => 'http://'.$request->getUri()->getHost().$this->router->pathFor('verify-email', ['code' => $user->email_token])]);

        $this->router->redirectTo();

        return $response;
    }
}
