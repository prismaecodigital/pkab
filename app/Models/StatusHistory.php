<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    use HasAdvancedFilter, HasFactory;

    public $table = 'status_histories';

    protected $appends = [
        'status_label',
    ];

    protected $orderable = [
        'id',
        'pkab_id',
        'status',
    ];

    protected $filterable = [
        'id',
        'pkab_id',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'pkab_id',
        'status',
        'user_id',
        'created_at',
        'updated_at',
    ];

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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function pkab()
    {
        return $this->belongsTo(PkabItem::class);
    }

    public function getStatusLabelAttribute()
    {
        return collect(static::STATUS_SELECT)->firstWhere('value', $this->status)['label'] ?? '';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.date_format')) : null;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }
}