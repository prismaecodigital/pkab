<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class MarketlistItem extends Model
{
    use HasAdvancedFilter, HasFactory;

    public $table = 'marketlist_items';

    protected $orderable = [
        'name',
        'category.name'
    ];

    protected $filterable = [
        'name',
        'category.name'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'category_id',
        'created_at',
        'updated_at'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
