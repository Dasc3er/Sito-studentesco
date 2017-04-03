<?php

namespace App;

abstract class Middleware extends App
{
    abstract public function __invoke($request, $response, $next);
}
