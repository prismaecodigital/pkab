<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasAdvancedFilter, Notifiable, HasFactory, HasApiTokens;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $orderable = [
        'id',
        'name',
        'email',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $filterable = [
        'id',
        'name',
        'username',
        'email',
        'no_hp',
        'roles.title',
    ];

    protected $fillable = [
        'name',
        'username',
        'email',
        'no_hp',
        'notifable_email',
        'notifable_wa',
        'has_completed_survey',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('title', 'Admin')->exists();
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = Hash::needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('title', $roleName)->exists();
    }

    public function dept()
    {
        return $this->belongsToMany(Dept::class);
    }

    public function bu()
    {
        return $this->belongsToMany(Bu::class);
    }

}