<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $fillable = ['last_active', 'session_code'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
