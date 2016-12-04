<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    protected $connection = 'default';
    protected $table = 'user_courses';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }
}
