<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static $rules = [
        'name' => 'string|required|min:2',
        'email' => 'email|required|unique:users',
        'password' => 'string|min:8'
    ];

    public static function getRules() 
    {
        return self::$rules;
    }

}
