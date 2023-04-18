<?php

namespace App\Observers;

use App\Models\PkabItem;
use App\Models\StatusHistory;
use App\Models\User;
use App\Notifications\DataChangeEmailNotification;
use Illuminate\Support\Facades\Notification;

class PkabItemActionObserver
{
    public function created(PkabItem $pkab)
    {
        $statusVal = array_search($pkab->status, array_column(PkabItem::STATUS_SELECT, 'value'));
        $status = PkabItem::STATUS_SELECT[$statusVal]['label'];
        $users = User::whereHas('roles', function ($q) use($pkab) {
            return $q->where('title', 'Admin')->orWhere('title', 'Dept Head');
        })->whereHas('dept', function ($q) use($pkab) {
            return $q->where('id', $pkab->dept_id);
        })->get();
        $data  = ['action' => 'dibuat', 'id'=> $pkab->id, 'code' => $pkab->code, 'status' => $status, 'user' => $pkab->user->name, 'users' => $users];
        Notification::send($users, new DataChangeEmailNotification($data));
    }

    public function updated(PkabItem $pkab)
    {
        $statusVal = array_search($pkab->status, array_column(PkabItem::STATUS_SELECT, 'value'));
        $status = PkabItem::STATUS_SELECT[$statusVal]['label'];
        $user_who_update = StatusHistory::with('user')->where('pkab_id', $pkab->id)->where('status', $pkab->status)->first();
        $user_name = $user_who_update->user->name;
        $users = User::whereHas('roles', function ($q) use($pkab) {
            return $q->whereHas('permissions', function ($q) use($pkab) {
                return $q->where('title', $pkab->status);
            });
        })->whereHas('dept', function ($q) use($pkab) {
            return $q->where('id', $pkab->dept_id);
        })->get();
        $data  = ['action' => 'diproses', 'id'=> $pkab->id, 'code' => $pkab->code, 'status' => $status, 'user' => $user_name, 'users' => $users];
        Notification::send($users, new DataChangeEmailNotification($data));
    }
}