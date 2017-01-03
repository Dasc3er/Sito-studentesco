<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $connection = 'default';

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course');
    }
}
