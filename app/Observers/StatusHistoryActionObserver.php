<?php

namespace App\Observers;

use App\Models\PkabItem;
use App\Models\StatusHistory;
use App\Models\User;
use App\Notifications\DataChangeEmailNotification;
use App\Notifications\WhacenterNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class StatusHistoryActionObserver
{
    public function created(StatusHistory $statusHistory)
    {
        $pkab = $statusHistory->pkab;
        $statusVal = array_search($pkab->status, array_column(PkabItem::STATUS_SELECT, 'value'));
        $status = PkabItem::STATUS_SELECT[$statusVal]['label'];

        if($status != 'Dibatalkan') {
            $users = User::where(function ($query) {
                $query->where('notifable_email', true)
                      ->orWhere('notifable_wa', true);
                })
                ->whereHas('roles', function ($q) use($statusHistory) {
                return $q->whereHas('permissions', function ($q) use($statusHistory) {
                    return $q->where('title', $statusHistory->status);
                });
                })->whereHas('dept', function ($q) use($pkab) {
                    return $q->where('id', $pkab->dept_id);
                })->get();
            $ket = '';
            $action = 'diproses';
        }
        if($status === 'Dibatalkan') {
            $users = User::where('id', $pkab->user_id)->get();
            $ket = $pkab->ket;
            $action = 'dibatalkan';
        }
        $data  = ['action' => $action, 'id' => $pkab->id, 'code' => $pkab->code, 'status' => $status, 'user' => $statusHistory->user->name, 'ket' => $ket];
        // Send Email Notification if notifable_email is true
        foreach ($users as $user) {
            if ($user->notifable_email) {
                Notification::send($user, new DataChangeEmailNotification($data));
            }
        }

        // Send WhatsApp Notification if notifable_wa is true
        foreach ($users as $user) {
            if ($user->notifable_wa) {
                $data['phone'] = $user->no_hp;
                Notification::send($user, new WhacenterNotification($data));
                Log::info("testing sending whatsapp message");
            }
        }
        // Notification::send($users, new DataChangeEmailNotification($data));
    }
}