<?php

namespace App\Notifications;

use App\Onesignaldata;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use Illuminate\Notifications\Notification;

use App\Message;
use App\Sheep;
use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class onesignaltest extends Notification
{
    use Queueable,Dispatchable, InteractsWithSockets, SerializesModels;
//    private $data; //  //this is the "model" data that will be passed through the notify method

    public $sheep;

    public $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Sheep $sheep, Message $message)
    {
        $this->sheep = $sheep;
        $this->message = $message;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['OneSignalServiceProvider::class'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
//    public function toOneSignal($notifiable)
//    {
//        return OneSignalMessage::create()
//            ->subject("Your {$notifiable->service} account was approved!")
//            ->body("Click here to see details.")
//            ->url('http://onesignal.com')
//            ->webButton(
//                OneSignalWebButton::create('link-1')
//                    ->text('Click here')
//                    ->icon('https://upload.wikimedia.org/wikipedia/commons/4/4f/Laravel_logo.png')
//                    ->url('http://laravel.com')
//            );    }


    public function toOneSignal()
    {
        OneSignal::sendNotificationToAll(
            "如果看到了，就是我出運了",
            $url = null,
            $data = null,
            $buttons = null,
            $schedule = null
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
