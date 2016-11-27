<?php

namespace App\Middlewares\Permissions;

class GuestMiddleware extends UserMiddleware
{
    protected function operation($request, $response)
    {
        $this->flash->addMessage('errors', $this->translator->translate('base.please-logout'));
        $response = $this->router->redirect($response);

        return $response;
    }

    protected function hasPermission()
    {
        return !parent::hasPermission();
    }
}
