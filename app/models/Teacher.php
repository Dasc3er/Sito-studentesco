<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $connection = 'default';

    public function quotes()
    {
        return $this->hasMany('App\Models\Quote');
    }
}
