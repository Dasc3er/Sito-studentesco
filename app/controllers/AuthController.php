<?php

namespace App\Controllers;

use App\Models;

class AuthController extends \App\App
{
    public function login($request, $response, $args)
    {
        if (empty($_SESSION['try'])) {
            $_SESSION['try'] = 0;
        }

        if (intval($_SESSION['try']) != 0 && intval($_SESSION['try']) % 3 == 0) {
            $time = 180 + (30 * (intval($_SESSION['try']) / 3 - 1));

            $args['minutes'] = floor($time / 60);
            $args['seconds'] = floor($time % 60);
            $args['time'] = $time;

            $_SESSION['time'] = strtotime('+'.floor($time / 60).' Minutes +'.floor($time % 60).' Seconds', strtotime('now'));
        }

        $response = $this->view->render($response, 'auth/login.twig', $args);

        return $response;
    }

    public function loginPost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            $email = $this->filter->email;
            $password = $this->filter->password;

            if (!empty($email) && !empty($password) && (empty($_SESSION['time']) || $_SESSION['time'] < strtotime('now'))) {
                $auth = $this->auth->attempt($email, $password);

                if ($auth) {
                    $this->flash->addMessage('infos', $this->translator->translate('login.success'));
                    $this->router->redirectTo();

                    session_regenerate_id();
                } else {
                    $this->flash->addMessage('errors', $this->translator->translate('login.error'));

                    if (!empty($_SESSION['try'])) {
                        $_SESSION['try'] = intval($_SESSION['try']) + 1;
                    }

                    $this->router->redirectTo('login');
                }
            }
        } else {
            $this->router->redirectTo('login');
        }

        return $response;
    }

    public function logout($request, $response, $args)
    {
        $this->auth->logout();
        $this->flash->addMessage('infos', $this->translator->translate('logout.success'));

        $this->router->redirectTo();

        return $response;
    }

    public function register($request, $response, $args)
    {
        $response = $this->view->render($response, 'auth/registration.twig', $args);

        return $response;
    }

    public function registerPost($request, $response, $args)
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
                $user = new Models\User();

                $user->name = $name;
                $user->username = $username;
                $user->email = $email;
                $user->password = $password;
                $user->email_token = secure_random_string();
                $user->role = 0;

                $user->save();

                \Utils::sendEmail($email, 'verification', [':path' => 'http://'.$request->getUri()->getHost().$this->router->pathFor('verify-email', ['code' => $user->email_token])]);

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

                $this->router->redirectTo('registration');
            }
        }

        return $response;
    }

    public function retrieve($request, $response, $args)
    {
        $response = $this->view->render($response, 'auth/retrieve.twig', $args);

        return $response;
    }

    public function retrievePost($request, $response, $args)
    {
        if (!$this->validator->hasErrors()) {
            $email = $this->filter->email;

            $result = Models\User::where(['email' => \App\App::encode($email)])->first();
            if (!empty($result)) {
                $token = secure_random_string();

                $result->reset_token = $token;
                $result->save();

                \Utils::sendEmail($email, 'reset', [':path' => 'http://'.$request->getUri()->getHost().$this->router->pathFor('retrieve', ['token' => $token])]);

                $this->flash->addMessage('infos', $this->translator->translate('retrieve.success'));
                $this->router->redirectTo();
            } else {
                $this->router->redirectTo('retrieve');
            }

            return $response;
        }
    }

    public function retrieveToken($request, $response, $args)
    {
        $response = $this->view->render($response, 'users/credentials.twig', $args);

        return $response;
    }

    public function retrieveTokenPost($request, $response, $args)
    {
        $password = $this->filter->password;
        $rep_password = $this->filter->rep_password;

        if (!$this->validator->hasErrors() && $password == $rep_password) {
            $result = Models\User::where(['reset_token' => $args['token']])->first();

            $result->password = $password;
            $result->reset_token = '';
            $result->save();

            $this->flash->addMessage('infos', $this->translator->translate('credentials.success'));
            $this->router->redirectTo();
            session_regenerate_id();
        } else {
            if ($password != $rep_password) {
                $this->flash->addMessage('errors', $this->translator->translate('errorPassword'));
            } else {
                $this->flash->addMessage('errors', $this->translator->translate('credentials.error'));
            }

            $this->router->redirectTo('recupero', ['token' => $args['token']]);
        }

        return $response;
    }
}
