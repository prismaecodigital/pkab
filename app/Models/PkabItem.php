<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkabItem extends Model
{
    use HasAdvancedFilter, HasFactory;

    public $table = 'pkab_items';

    protected $appends = [
        'status_label',
        'status_value',
    ];

    protected $dates = [
        'req_date',
        'created_at',
        'updated_at',
    ];

    protected $orderable = [
        'id',
        'code',
        'req_date',
        'user.name',
        'status',
        'dept.name',
        'site.name',
        'created_at',
        'updated_at',
    ];

    protected $filterable = [
        'id',
        'code',
        'req_date',
        'user.name',
        'status',
        'dept.name',
        'bu.name',
        'site.name',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'req_date',
        'code',
        'user_id',
        'status',
        'status_label',
        'ket',
        'bu_id',
        'dept_id',
        'site_id',
        'created_at',
        'updated_at',
    ];

    public const SATUAN_SELECT = ['UNIT','PCS','EKOR','KG','IKET','PACK','BTL','BAL', 'GRAM', 'SISIR', 'PAPAN', 'TABLET', 'BOX' ,'STRIP', 'METER', 'PAIL'];

    public const STATUS_SELECT = [
        [
            'label' => 'Menunggu Persetujuan Leader',
            'value' => 'leader_acc',
        ],
        [
            'label' => 'Pengajuan FPBD',
            'value' => 'purchasing_acc_1',
        ],
        [
            'label' => 'Menunggu Dana',
            'value' => 'purchasing_acc_2',
        ],
        [
            'label' => 'Proses Pembelian',
            'value' => 'purchasing_acc_3',
        ],
        [
            'label' => 'Menunggu konfirmasi penerimaan',
            'value' => 'user_acc',
        ],
        [
            'label' => 'Dibatalkan',
            'value' => 'cancel',
        ],
        [
            'label' => 'Selesai',
            'value' => 'selesai',
        ],
    ];

    public function scopeFilterByStatusLabel($query, $statusLabel)
    {
        return $query->where('status', $statusLabel);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute()
    {
        return collect(static::STATUS_SELECT)->firstWhere('value', $this->status)['label'] ?? '';
    }

    public function getStatusValueAttribute()
    {
        return collect(static::STATUS_SELECT)->firstWhere('label', $this->status)['value'] ?? '';
    }

    public function dept()
    {
        return $this->belongsTo(Dept::class);
    }

    public function bu()
    {
        return $this->belongsTo(Bu::class);
    }

    public function getReqDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d-m-Y') : null;
    }

    public function setReqDateAttribute($value)
    {
        $this->attributes['req_date'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d-m-Y') : null;
    }

    public function items()
    {
        return $this->hasMany(Item::class,'pkab_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(StatusHistory::class, 'pkab_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}