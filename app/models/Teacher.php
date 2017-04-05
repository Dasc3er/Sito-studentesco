<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected static function boot() {
        parent::boot();

        static::deleting(function($teacher) {
            $teacher->quotes()->delete();
        });
    }

    public function quotes()
    {
        return $this->hasMany('App\Models\Quote');
    }

     public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
