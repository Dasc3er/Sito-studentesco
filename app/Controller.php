<?php

namespace App;

abstract class Controller extends App
{
    public function __construct($container)
    {
        parent::__construct($container);

        \Illuminate\Pagination\Paginator::currentPageResolver(function ($this) {
            return $this->filter->page;
        });
    }
}
