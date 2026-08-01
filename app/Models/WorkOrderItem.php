<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'mechanic_id',
        'item_type',
        'reference_id',
        'item_name',
        'qty',
        'cost_price',
        'sell_price',
        'commission_amount',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'float',
        'cost_price' => 'float',
        'sell_price' => 'float',
        'commission_amount' => 'float',
        'subtotal' => 'float',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }
}
