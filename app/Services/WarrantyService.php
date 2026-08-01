<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Illuminate\Support\Carbon;

class WarrantyService
{
    public function findEligibleWarrantyInvoices(string $plateNumber)
    {
        $vehicle = Vehicle::where('plate_number', $plateNumber)->first();

        if (!$vehicle) {
            return collect();
        }

        $twoWeeksAgo = Carbon::now()->subDays(14);

        return Invoice::whereHas('workOrder', function ($q) use ($vehicle) {
            $q->where('vehicle_id', $vehicle->id);
        })
        ->where('created_at', '>=', $twoWeeksAgo)
        ->with(['workOrder.vehicle.customer', 'workOrder.items'])
        ->get();
    }

    public function createWarrantyWorkOrder(int $vehicleId, int $parentInvoiceId, float $initialEstimate = 0): WorkOrder
    {
        return WorkOrder::create([
            'wo_number'         => 'WO-WAR-' . strtoupper(uniqid()),
            'vehicle_id'        => $vehicleId,
            'initial_estimate'  => $initialEstimate,
            'final_cost'        => 0,
            'status'            => 'queue',
            'is_warranty_claim' => true,
            'parent_invoice_id' => $parentInvoiceId,
        ]);
    }
}
