<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Carbon\Carbon;

class MarketlistOrderItem extends Model
{
    use HasFactory, HasAdvancedFilter;

    public $table = 'marketlist_order_item';

    protected $orderable = [
        'ml_id',
        'item_id',
        'required_date',
        'qty',
        'approved_qty',
        'approved_date',
        'notes',
    ];

    protected $filterable = [
        'ml_id',
        'item_id',
        'required_date',
        'approved_qty',
        'approved_date',
        'notes',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'required_date',
        'approved_date'
    ];

    protected $fillable = [
        'ml_id',
        'item_id',
        'required_date',
        'qty',
        'satuan',
        'approved_qty',
        'approved_date',
        'notes',
        'created_at',
        'updated_at'
    ];

    public function marketlist()
    {
        return $this->belongsTo(Marketlist::class, 'ml_id');
    }

    public function item()
    {
        return $this->belongsTo(MarketlistItem::class, 'item_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getRequiredDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d-m-Y') : null;
    }

    public function setRequiredDateAttribute($value)
    {
        $this->attributes['required_date'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getApprovedDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d-m-Y') : null;
    }

    public function setApprovedDateAttribute($value)
    {
        $this->attributes['approved_date'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d-m-Y') : null;
    }
}