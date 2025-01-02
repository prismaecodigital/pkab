<?php

namespace App\Broadcasting;

use App\Notifications\WhacenterNotification;

class WhacenterChannel
{
    public function send($notifiable, WhacenterNotification $notification)
    {
        $message = $notification->toWhatsapp($notifiable);

        if (!$message instanceof \App\Services\WhacenterService) {
            throw new \Exception('The WhacenterService object was not properly returned.');
        }

        $message->send();
    }
}
