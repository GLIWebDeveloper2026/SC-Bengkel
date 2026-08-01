<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'wo_number',
        'vehicle_id',
        'initial_estimate',
        'final_cost',
        'status',
        'is_warranty_claim',
        'parent_invoice_id',
    ];

    protected $casts = [
        'initial_estimate' => 'float',
        'final_cost' => 'float',
        'is_warranty_claim' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items()
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function parentInvoice()
    {
        return $this->belongsTo(Invoice::class, 'parent_invoice_id');
    }
}
