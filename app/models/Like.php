<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $connection = 'default';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function quote()
    {
        return $this->belongsTo('App\Models\Quote');
    }
}
