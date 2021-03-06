<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function likes()
    {
        return $this->belongsToMany('App\Models\User');
    }
}
