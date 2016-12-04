<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $connection = 'default';

    public function school()
    {
        return $this->belongsTo('App\Models\School');
    }
}
