<?php

namespace App\Core;

class BaseContainer
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        if ($property == 'settings') {
            return AppContainer::settings();
        } elseif (isset($this->container[$property])) {
            return $this->container[$property];
        }
    }
}
