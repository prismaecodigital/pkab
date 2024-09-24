<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Marketlist extends Model
{
    use HasAdvancedFilter, HasFactory;

    public $table = 'marketlist_order';

    protected $appends = [
        'status_label',
    ];

    protected $orderable = [
        'code',
        'event',
        'bu_id',
        'dept_id',
        'user_id',
        'site_id',
        'status',
        'created_at',
        'deleted_at'
    ];

    protected $dates = [
        'created_at',
        'deleted_at'
    ];

    protected $fillable = [
        'code',
        'event',
        'bu_id',
        'dept_id',
        'site_id',
        'user_id',
        'status',
        'created_at',
        'deleted_at'
    ];

    protected $filterable = [
        'code',
        'event',
        'bu.name',
        'dept.name',
        'site.name',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'bu_id' => 'integer',
        'dept_id' => 'integer',
        'site_id' => 'integer',
        'user_id' => 'integer'
    ];

    public const SATUAN_SELECT = ['PCS','EKOR','KG','IKET','PACK','BTL','BAL', 'GRAM', 'SISIR', 'PAPAN','METER','PAIL'];

    public const STATUS_SELECT = [
        [
            'label' => 'Menunggu Purchasing',
            'value' => 'purchasing_ml_1',
        ],
        [
            'label' => 'Menunggu Barang dikirim',
            'value' => 'purchasing_ml_2',
        ],
        [
            'label' => 'Menunggu Konfirmasi User',
            'value' => 'user_acc',
        ],
        [
            'label' => 'Selesai',
            'value' => 'selesai',
        ],
        [
            'label' => 'Dibatalkan',
            'value' => 'cancel',
        ],
    ];

    public function scopeFilterByStatusLabel($query, $statusLabel)
    {
        return $query->where('status', $statusLabel);
    }

    public function bu()
    {
        return $this->belongsTo(Bu::class, 'bu_id');
    }

    public function dept()
    {
        return $this->belongsTo(Dept::class, 'dept_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function marketlistItems()
    {
        return $this->belongsToMany(MarketlistItem::class, 'marketlist_order_item', 'ml_id', 'item_id');
    }

    public function items()
    {
        return $this->hasMany(MarketlistOrderItem::class, 'ml_id', 'id')->with('item');
    }
    
    public function getStatusLabelAttribute()
    {
        return collect(static::STATUS_SELECT)->firstWhere('value', $this->status)['label'] ?? '';
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d-m-Y') : null;
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

}
