<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DataChangeEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return $this->getMessage();
    }

    public function getMessage()
    {
        return (new MailMessage)
            ->subject('PKAB Notification')
            ->greeting('Hello, ')
            ->line('PKAB nomor ' . $this->data['code'] . ' telah ' . $this->data['action'] . ' oleh ' . $this->data['user'] . ' dan saat ini ' . $this->data['status'])
            ->line('Silahkan diproses lebih lanjut.')
            ->action(config('app.name'), (config('app.url').'/admin/pkab/pkab-items/'.$this->data['id']))
            ->line('Thank you')
            ->line(config('app.name') . ' Team')
            ->salutation(' ');
    }
}