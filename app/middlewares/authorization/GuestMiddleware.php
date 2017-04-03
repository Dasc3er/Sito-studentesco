<?php

namespace App\Middlewares\Authorization;

class GuestMiddleware extends UserMiddleware
{
    protected function operation($request, $response)
    {
        $this->flash->addMessage('errors', $this->translator->translate('base.please-logout'));
        $response = $this->router->redirect($response);

        return $response;
    }

    protected function hasAuthorization()
    {
        return !parent::hasAuthorization();
    }
}
