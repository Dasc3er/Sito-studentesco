<?php

namespace App\Middlewares\Permissions;

class UserMiddleware extends \App\Core\PermissionMiddleware
{
    protected function operation($request, $response)
    {
        $this->flash->addMessage('errors', $this->translator->translate('base.please-login'));
        $response = $this->router->redirect($response, 'login');

        return $response;
    }

    protected function hasPermission()
    {
        return $this->auth->check();
    }
}
