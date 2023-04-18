<?php

namespace App\Observers;

use App\Models\PkabItem;
use App\Models\StatusHistory;
use App\Models\User;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class StatusHistoryActionObserver
{
    public function created(StatusHistory $statusHistory)
    {
        $pkab = $statusHistory->pkab;
        $statusVal = array_search($pkab->status, array_column(PkabItem::STATUS_SELECT, 'value'));
        $status = PkabItem::STATUS_SELECT[$statusVal]['label'];

        if($status != 'Dibatalkan') {
            $users = User::whereHas('roles', function ($q) use($statusHistory) {
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
        $data  = ['action' => $action, 'id' => $pkab->id, 'code' => $pkab->code, 'status' => $status, 'user' => $statusHistory->user->name, 'users' => $users, 'ket' => $ket];
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}