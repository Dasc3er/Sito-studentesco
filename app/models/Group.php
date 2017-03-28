<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function school()
    {
        return $this->belongsTo('App\Models\School');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
}
