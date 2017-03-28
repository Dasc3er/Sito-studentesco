<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course');
    }
}
