<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $connection = 'default';

    public function courses()
    {
        return $this->belognsTo('App\Models\Course');
    }

    public function founder()
    {
        return $this->hasOne('App\Models\User');
    }

    public function users()
    {
        return $this->hasManyThrough('App\Models\Users', 'App\Models\UserTeam');
    }
}
