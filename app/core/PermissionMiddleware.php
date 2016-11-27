<?php

namespace App\Core;

abstract class PermissionMiddleware extends BaseContainer
{
    public function __invoke($request, $response, $next)
    {
        if (!$this->hasPermission()) {
            $response = $this->operation($request, $response);
        } else {
            $response = $next($request, $response);
        }

        return $response;
    }

    abstract protected function operation($request, $response);

    abstract protected function hasPermission();
}
