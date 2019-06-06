<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

//abstract class User extends Model implements
//    AuthenticatableContract,
//    AuthorizableContract,
//    CanResetPasswordContract,
//    AuthenticatableUserContract
class User extends Authenticatable implements JWTSubject
{
    //use Authorizable, CanResetPassword, Notifiable;
    use Notifiable;

//    protected $fillable = [
//        'username', 'email', 'password',
//    ];
    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'email_verified_at'
    ];


    public function libraries()
    {
        return $this->hasMany('App\Models\Library');
    }

    public function friends_user()
    {
        return $this->hasMany('App\Models\Friends');
    }

    public function friends_friend()
    {
        return $this->hasMany('App\Models\Friends');
    }

    public function plays()
    {
        return $this->hasMany('App\Models\Play');
    }

    public function players()
    {
        return $this->hasMany('App\Models\Players');
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
