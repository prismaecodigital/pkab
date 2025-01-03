<?php

namespace App\Notifications;

use App\Broadcasting\WhacenterChannel;
use App\Services\WhacenterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhacenterNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WhacenterChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toWhatsapp(object $notifiable): WhacenterService
    {
        $service = new WhacenterService();

        try {
            if ($this->data['ket'] == '') {
                return $service->to($this->data['phone'])
                ->message("Hai, \n\n" .
                        "Ada informasi penting nih! PKAB dengan nomor `" . $this->data['code'] . "` " .
                        "sudah *" . $this->data['action'] . "* oleh _" . $this->data['user'] . "_ " .
                        "dan saat ini berada dalam status *" . $this->data['status'] . "*. \n\n" .
                        "Yuk, segera cek detailnya dan lakukan langkah berikutnya di sini: " .
                        config('app.url') . "/admin/pkab/pkab-items/" . $this->data['id'] . "\n\n" .
                        "Terima kasih ya! 😊\n" .
                        "Salam, RAISA\n\n" .
                        "_Ini pesan otomatis, jangan dibalas ya!_");
            }

            if ($this->data['ket'] != '') {
                return $service->to($this->data['phone'])
                ->message("Hai, \n\n" .
                        "Maaf, PKAB dengan nomor `" . $this->data['code'] . "` " .
                        "telah *" . $this->data['action'] . "* oleh _" . $this->data['user'] . "_. \n" .
                        "Alasan penolakan: *" . $this->data['ket'] . "*.\n\n" .
                        "Detail PKAB bisa dicek di sini: \n" .
                        config('app.url') . "/admin/pkab/pkab-items/" . $this->data['id'] . "\n\n" .
                        "Terima kasih ya! 😊\n" .
                        "Salam, RAISA\n\n" .
                        "_Ini pesan otomatis, jangan dibalas ya!_");
            }

        } catch (\Exception $e) {
            Log::error("Failed to create Whacenter notification: " . $e->getMessage());
            throw $e;  // Re-throw the exception to allow Laravel to handle it.
        }
    }
}