<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'purchase_unit',
        'sell_unit',
        'conversion_factor',
        'stock_qty',
        'buy_price',
        'sell_price',
    ];

    protected $casts = [
        'conversion_factor' => 'float',
        'stock_qty' => 'float',
        'buy_price' => 'float',
        'sell_price' => 'float',
    ];
}
