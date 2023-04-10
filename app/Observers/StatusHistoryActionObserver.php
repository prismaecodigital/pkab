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
        $users = User::whereHas('roles', function ($q) use($pkab) {
            return $q->where('title', 'Admin')->orWhere('title', 'Dept Head');
        })->whereHas('dept', function ($q) use($pkab) {
            return $q->where('id', $pkab->dept_id);
        })->get();
        $data  = ['action' => 'diproses', 'id' => $pkab->id, 'code' => $pkab->code, 'status' => $status, 'user' => $statusHistory->user->name, 'users' => $users];
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}