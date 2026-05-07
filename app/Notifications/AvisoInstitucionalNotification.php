<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class AvisoInstitucionalNotification extends Notification
{
    use Queueable;

    protected $aviso;

    public function __construct($aviso)
    {
        $this->aviso = $aviso;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new \NotificationChannels\WebPush\WebPushMessage)
            ->title('Nueva Circular - CEAA')
            ->icon('/img/logo-ceaa.png')
            ->body($this->aviso->titulo)
            ->data(['action_url' => route('avisos.index')])
            ->options(['TTL' => 1000]);
    }
}