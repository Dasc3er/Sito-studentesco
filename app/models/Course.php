<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $connection = 'default';

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    public function school()
    {
        return $this->belongsTo('App\Models\School');
    }

    public function teams()
    {
        return $this->hasMany('App\Models\Team');
    }

    public function users()
    {
        return $this->hasManyThrough('App\Models\Users', 'App\Models\UserCourse');
    }
}
