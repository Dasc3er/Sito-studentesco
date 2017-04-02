<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $hidden = ['password', 'role', 'state'];
    protected $fillable = ['group_id'];

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

    public function group()
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
        $this->attributes['password'] = \App\App::hashpassword($value);
    }

    /**
     * Set the user's email.
     *
     * @param string $value
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = \App\App::encode($value);
    }

    /**
     * Get the user's email.
     *
     * @param string $value
     */
    public function getEmailAttribute($value)
    {
        return \App\App::decode($value);
    }

    /**
     * Set the user's email.
     *
     * @param string $value
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = \App\App::encode($value);
    }

    /**
     * Get the user's email.
     *
     * @param string $value
     */
    public function getUsernameAttribute($value)
    {
        return \App\App::decode($value);
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

    public function isAdmin()
    {
        return !empty($this->is_admin);
    }

    /**
     * Controlla che l'username inserito sia univoco.
     *
     * @param string $username Username da controllare
     * @param bool    $ignore_current  Ignora o meno l'utente attuale
     *
     * @return bool
     */
    public static function isUsernameFree($username, $ignore_current = true)
    {
        $where = [['username', '=', \App\App::encode($username)]];

        $auth = \App\App::getContainer()->auth;
        if ($auth->check() && !empty($ignore_current)) {
            $where[] = ['id', '!=', $auth->user()->id];
        }

        return User::where($where)->count() == 0;
    }

    /**
     * Controlla che l'username inserito sia univoco.
     *
     * @param string $username Username da controllare
     * @param bool    $ignore_current  Ignora o meno l'utente attuale
     *
     * @return bool
     */
    public static function isEmailFree($email, $ignore_current = true)
    {
        $where = [['email', '=', \App\App::encode($email)]];

        $auth = \App\App::getContainer()->auth;
        if ($auth->check() && !empty($ignore_current)) {
            $where[] = ['id', '!=', $auth->user()->id];
        }

        return User::where($where)->count() == 0;
    }

    public function isSubscribedTo($course)
    {
        $users = array_pluck($course->users()->get()->toArray(), 'id');

        return in_array($this->id, $users);
    }

    public function isFreeTime($course, $event = null)
    {
        if (empty($event)) {
            $event = Event::orderBy('date', 'desc')->first();
            if (empty($event)) return false;
        }

        if ($course->event_id != $event->id) {
            return false;
        }

        if ($course->users()->count() >= $course->capacity) {
            return false;
        }

        if (!isset($this->group_id)) {
            return false;
        }

        $courses = $this->courses()->with(['event' => function ($query) use ($event) {
            $query->where('id', $event->id);
        }, 'times'])->get();

        $times = [];
        foreach ($courses as $c) {
            $t = array_pluck($c->times()->get()->toArray(), 'id');
            $times = array_merge($times, $t);
        }

        $times = array_unique($times);
        $course_time = array_pluck($course->times()->get()->toArray(), 'id');

        foreach ($times as $time) {
            if (in_array($time, $course_time)) {
                return false;
            }
        }

        return true;
    }
}
