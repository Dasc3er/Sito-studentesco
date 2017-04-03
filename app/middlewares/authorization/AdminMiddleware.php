<?php

namespace App\Middlewares\Authorization;

class AdminMiddleware extends UserMiddleware
{
    protected function hasAuthorization()
    {
        return parent::hasAuthorization() && $this->auth->isAdmin();
    }
}
