<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOption extends Model
{
    protected $connection = 'default';
    protected $table = 'user_options';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function option()
    {
        return $this->belongsTo('App\Models\Option');
    }
}
