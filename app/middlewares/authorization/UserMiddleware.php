<?php

namespace App\Middlewares\Authorization;

class UserMiddleware extends \App\Middlewares\AuthorizationMiddleware
{
    protected function operation($request, $response)
    {
        $this->flash->addMessage('errors', $this->translator->translate('base.please-login'));
        $response = $this->router->redirect($response, 'login');

        return $response;
    }

    protected function hasAuthorization()
    {
        return $this->auth->check();
    }
}
