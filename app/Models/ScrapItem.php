<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'qty',
        'collected_date',
        'sold_date',
        'sale_amount',
    ];

    protected $casts = [
        'collected_date' => 'datetime',
        'sold_date' => 'datetime',
        'sale_amount' => 'float',
    ];
}
