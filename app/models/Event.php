<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $connection = 'default';

    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }
}
