<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionUser extends Model
{
    protected $connection = 'default';
    protected $table = 'option_user';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function option()
    {
        return $this->belongsTo('App\Models\Option');
    }
}
