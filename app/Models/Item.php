<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasAdvancedFilter, HasFactory;

    public $table = 'items';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $orderable = [
        'id',
        'name',
        'merk',
        'spesifikasi',
        'qty',
    ];

    protected $filterable = [
        'id',
        'name',
        'merk',
        'spesifikasi',
        'qty',
    ];

    protected $fillable = [
        'pkab_id',
        'name',
        'merk',
        'spesifikasi',
        'qty',
        'pkab_id',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function pkab()
    {
        return $this->belongsTo(PkabItem::class);
    }
}