<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }
}
