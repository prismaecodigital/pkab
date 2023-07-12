<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasAdvancedFilter, HasFactory;

    public $table = 'site';

    protected $orderable = [
        'id',
        'name',
        'bu_id',
    ];

    protected $filterable = [
        'id',
        'name',
        'bu_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'bu_id',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function bu()
    {
        return $this->belongsTo(Bu::class);
    }

}