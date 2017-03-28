<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public function quotes()
    {
        return $this->hasMany('App\Models\Quote');
    }
}
