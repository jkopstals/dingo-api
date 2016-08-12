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

    /**
     * Validation rules for users
     *
     * @var array
     */
    protected static $rules = [
        'name' => 'string|required|min:2',
        'email' => 'email|required|unique:users',
        'password' => 'string|min:8',
        'password_confirmation' => 'required|same:password'
    ];

    /**
     * Get array of validation rules for user creation
     * 
     * @return array
     */
    public static function getRules() 
    {
        return self::$rules;
    }
    
    /**
     * Password mutator
     * Automatically encrypt password whenever user is created/updated
     * 
     * @param type $password
     */
    public function setPasswordAttribute($password)
    {   
        $this->attributes['password'] = bcrypt($password);
    }

}
