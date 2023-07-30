<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;


class Category extends Model
{
    use HasFactory, HasAdvancedFilter;

    public $tables = 'categories';

    protected $orderable = [
        'id',
        'name'
    ];

    protected $fillable = [
        'name',
        'created_at',
        'deleted_at'
    ];

    protected $dates = [
        'created_date',
        'updated_date'
    ];

    protected $filterable = [
        'name'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
