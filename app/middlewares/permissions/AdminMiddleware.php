<?php

namespace App\Middlewares\Permissions;

class AdminMiddleware extends UserMiddleware
{
    protected function hasPermission()
    {
        return parent::hasPermission() && $this->auth->admin();
    }
}
