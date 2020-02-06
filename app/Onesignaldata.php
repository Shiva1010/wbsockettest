<?php

namespace App;

use App\Notifications\onesignaltest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Onesignaldata extends Model
{
    use Notifiable;

    public function sendNotification(){
        $this->notify(new onesignaltest($this));
        //Pass the model data to the OneSignal Notificator
    }

    public function routeNotificationForOneSignal()
    {
        /*
         * you have to return the one signal player id tat will
         * receive the message of if you want you can return
         * an array of players id
         */
//        return $this->data->user_one_signal_id;
        Notification::send($users, new ProductAdded($dataToNotify));
        return '7fe9685f-8403-42be-977e-299922e8dfeb';


    }
}
