<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

        protected static function boot() {
            parent::boot();

            static::deleting(function($course) {
                $course->users()->sync([]);
            });
        }
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
        return $this->belongsToMany('App\Models\User');
    }

    public function times()
    {
        return $this->belongsToMany('App\Models\Time');
    }
}
