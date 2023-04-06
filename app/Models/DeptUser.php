<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeptUser extends Model
{
    use HasFactory;

    public $table = 'dept_user';

    protected $orderable = [
        'user_id',
        'dept_id',
    ];

    protected $filterable = [
        'user_id',
        'dept_id',
    ];


    protected $fillable = [
        'user_id',
        'dept_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dept()
    {
        return $this->belongsTo(Dept::class);
    }
}
