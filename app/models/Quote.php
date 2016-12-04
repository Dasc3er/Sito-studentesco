<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $connection = 'default';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher');
    }
}
