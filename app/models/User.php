<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'default';
    protected $hidden = ['password', 'role', 'state'];

    public function logins()
    {
        return $this->hasMany('App\Models\Login');
    }

    public function quotes()
    {
        return $this->hasMany('App\Models\Quotes');
    }

    public function quotesLikes()
    {
        return $this->hasManyThrough('App\Models\Quotes', 'App\Models\Like');
    }

    public function options()
    {
        return $this->hasMany('App\Models\UserOption');
    }

    /**
     * Set the user's password (hash).
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Crypt::hashpassword($value);
    }

    /**
     * Set the user's email.
     *
     * @param string $value
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = \Crypt::encode($value);
    }

    /**
     * Get the user's email.
     *
     * @param string $value
     */
    public function getEmailAttribute($value)
    {
        return \Crypt::decode($value);
    }

    /**
     * Set the user's role.
     *
     * @param string $value
     */
    public function setRoleAttribute($value)
    {
        if (strtolower($value) == 'admin' || $value == 1) {
            $this->attributes['role'] = 1;
        } else {
            $this->attributes['role'] = 0;
        }
    }
}
