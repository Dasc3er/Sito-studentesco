<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $connection = 'default';

    public function logins()
    {
        return $this->hasMany('App\Models\UserOption');
    }
}
