<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sheep;

class SocialSheep extends Model
{
    protected $fillable = [
      'provider_sheep_id', 'provider','sheep_id'
    ];

    public function sheep(){

        return $this->belongsTo('App\Sheep');

    }
}
