<?php

namespace App;

abstract class Controller extends App
{
     public function __invoke()
    {
        \Illuminate\Pagination\Paginator::currentPageResolver(function ($this) {
            return $this->filter->page;
        });
    }
}
