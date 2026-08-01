<?php

namespace App\Services;

use App\Models\ApprovalLog;
use App\Models\Invoice;
use App\Models\Part;
use App\Models\ScrapItem;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    public function addLineItem(WorkOrder $wo, array $data): WorkOrderItem
    {
        return DB::transaction(function () use ($wo, $data) {
            $subtotal = $data['qty'] * $data['sell_price'];

            // Force service fee to 0 if this is a warranty claim and item is a service
            if ($wo->is_warranty_claim && $data['item_type'] === 'service') {
                $data['sell_price'] = 0;
                $subtotal = 0;
            }

            // Pre-check inventory stock capacity if item_type is inventory
            $part = null;
            if ($data['item_type'] === 'inventory') {
                if (!empty($data['reference_id'])) {
                    $part = Part::find($data['reference_id']);
                }
                if (!$part && !empty($data['item_name'])) {
                    $part = Part::where('name', 'LIKE', '%' . $data['item_name'] . '%')
                        ->orWhere('code', 'LIKE', '%' . $data['item_name'] . '%')
                        ->first();
                }

                if ($part && $data['qty'] > $part->stock_qty) {
                    throw new \InvalidArgumentException("Stok '" . $part->name . "' tidak mencukupi! Sisa stok di gudang hanya " . number_format($part->stock_qty, 2) . " " . $part->sell_unit . ", sedangkan permintaan sebesar " . number_format($data['qty'], 2) . " " . $part->sell_unit . ".");
                }
            }

            $item = WorkOrderItem::create([
                'work_order_id'     => $wo->id,
                'mechanic_id'       => $data['mechanic_id'] ?? null,
                'item_type'         => $data['item_type'],
                'reference_id'      => $part->id ?? ($data['reference_id'] ?? null),
                'item_name'         => $data['item_name'],
                'qty'               => $data['qty'],
                'cost_price'        => $data['cost_price'] ?? 0,
                'sell_price'        => $data['sell_price'],
                'commission_amount' => in_array($data['item_type'], ['service', 'inventory']) ? ($data['commission_amount'] ?? 0) : 0,
                'subtotal'          => $subtotal,
            ]);

            // Handle stock & scrap inventory behavior per item classifier
            if ($data['item_type'] === 'inventory' && $part) {
                $part->decrement('stock_qty', $data['qty']); // Decimal stock deduction (e.g. 0.8 Liter)
            } elseif ($data['item_type'] === 'trade_in') {
                ScrapItem::create([
                    'item_name' => $data['item_name'] ?? 'Aki Bekas',
                    'qty'       => 1,
                ]);
            }

            // Recalculate Work Order final cost
            $wo->update(['final_cost' => $wo->items()->sum('subtotal')]);

            return $item;
        });
    }

    public function requestApproval(WorkOrder $wo, string $itemName, float $estimatedCost): ApprovalLog
    {
        $wo->update(['status' => 'waiting_approval']);

        return ApprovalLog::create([
            'work_order_id'       => $wo->id,
            'requested_item_name' => $itemName,
            'estimated_cost'      => $estimatedCost,
            'status'              => 'PENDING',
        ]);
    }

    public function respondApproval(ApprovalLog $log, string $status, ?int $userId = null): ApprovalLog
    {
        return DB::transaction(function () use ($log, $status, $userId) {
            $log->update([
                'status'              => $status,
                'approved_by_user_id' => $userId,
            ]);

            $wo = $log->workOrder;

            if ($status === 'APPROVED') {
                $wo->update(['status' => 'working']);
            } elseif ($status === 'TIMEOUT_HOLD') {
                $wo->update(['status' => 'waiting_approval']);
            } else {
                $wo->update(['status' => 'working']);
            }

            return $log;
        });
    }

    public function generateInvoice(WorkOrder $wo): Invoice
    {
        return DB::transaction(function () use ($wo) {
            $wo->update(['status' => 'completed']);

            $totalAmount = $wo->items()->sum('subtotal');
            $customer = $wo->vehicle->customer;

            return Invoice::create([
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'work_order_id'  => $wo->id,
                'customer_id'    => $customer->id,
                'total_amount'   => $totalAmount,
                'paid_amount'    => 0,
                'balance_due'    => $totalAmount,
                'status'         => 'unpaid',
            ]);
        });
    }
}
