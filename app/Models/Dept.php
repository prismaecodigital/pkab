<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dept extends Model
{
    use HasAdvancedFilter, SoftDeletes, HasFactory;

    public $table = 'depts';

    protected $orderable = [
        'id',
        'name',
        'code',
    ];
    
    protected $casts = [
        'bu_id' => 'integer',
    ];

    protected $filterable = [
        'id',
        'name',
        'code',
        'bu.name',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'code',
        'bu_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function bu()
    {
        return $this->belongsTo(Bu::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}