<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTeam extends Model
{
    protected $connection = 'default';
    protected $table = 'user_teams';

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\team');
    }
}
