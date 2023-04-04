<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PkabCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pkab;
    
    public function __construct($pkab)
    {
        $this->pkab = $pkab;
    }

    public function handle()
    {
        // Get users with the relevant roles
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'role_a');
        })->get();
        
        // Send email notification to users
        foreach($users as $user) {
            Mail::to($user->email)->send(new PkabCreatedNotification($this->pkab));
        }
    }
}
