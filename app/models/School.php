<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $connection = 'default';

    public function classes()
    {
        return $this->hasMany('App\Models\Group');
    }

    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }
}
