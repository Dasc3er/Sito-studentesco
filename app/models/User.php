<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $hidden = ['password', 'role', 'state'];

    public function logins()
    {
        return $this->hasMany('App\Models\Login');
    }

    public function quotes()
    {
        return $this->hasMany('App\Models\Quote');
    }

    public function quotesLikes()
    {
        return $this->belongsToMany('App\Models\Quote');
    }

    public function options()
    {
        return $this->hasMany('App\Models\OptionUser');
    }

    public function courses()
    {
        return $this->belongsToMany('App\Models\Course');
    }

    public function groups()
    {
        return $this->belongsTo('App\Models\Course');
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

    public function isSubscribedTo($course)
    {
        $users = \Utils::array_pluck($course->users()->get()->toArray(), 'id');

        return in_array($this->id, $users);
    }

    public function isFreeTime($course, $event = null)
    {
        if (empty($event)) {
            $event = Event::orderBy('date', 'desc')->first();
        }

        if ($course->event_id != $event->id) {
            return false;
        }

        $courses = $this->courses()->with(['event' => function ($query) use ($event) {
            $query->where('id', $event->id);
        }, 'times'])->get();

        $times = [];
        foreach ($courses as $c) {
            $t = \Utils::array_pluck($c->times()->get()->toArray(), 'id');
            $times = array_merge($times, $t);
        }

        $times = array_unique($times);
        $course_time = \Utils::array_pluck($course->times()->get()->toArray(), 'id');

        foreach ($times as $time) {
            if (in_array($time, $course_time)) {
                return false;
            }
        }

        return true;
    }
}
