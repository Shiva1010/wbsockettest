<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\SocialSheep;


class Sheep extends Authenticatable
{
    use Notifiable;


    protected $fillable=[
        'fb_id','name','email','api_token','password','login_method',
    ];


    protected $hidden=[
        'password',
    ];

    /**
     * A user can have many messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function messages()
    {
        return $this->hasMany(Message::class);
    }



    protected $casts=[
      'email_verified_at' => 'datetime',
    ];

    public function socialsheep(){
        return $this->hasOne('App\SocialSheep');
    }




}
