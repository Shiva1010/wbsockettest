<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Sheep extends Authenticatable
{
    use Notifiable;


    protected $fillable=[
        'name','account','api_token','password',
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

}
