<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function processBulkPayment(int $customerId, float $totalPaid, array $invoiceIds, string $paymentMethod = 'CASH'): Payment
    {
        return DB::transaction(function () use ($customerId, $totalPaid, $invoiceIds, $paymentMethod) {
            $payment = Payment::create([
                'payment_number' => 'PAY-' . time(),
                'customer_id'    => $customerId,
                'total_paid'     => $totalPaid,
                'payment_method' => $paymentMethod,
                'payment_date'   => now(),
            ]);

            $remainingMoney = $totalPaid;
            $invoices = Invoice::whereIn('id', $invoiceIds)
                ->where('customer_id', $customerId)
                ->where('status', '!=', 'paid')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($invoices as $invoice) {
                if ($remainingMoney <= 0) {
                    break;
                }

                $allocated = min($remainingMoney, $invoice->balance_due);

                PaymentAllocation::create([
                    'payment_id'       => $payment->id,
                    'invoice_id'       => $invoice->id,
                    'amount_allocated' => $allocated,
                ]);

                $invoice->paid_amount += $allocated;
                $invoice->balance_due -= $allocated;
                $invoice->status = ($invoice->balance_due <= 0) ? 'paid' : 'partially_paid';
                $invoice->save();

                $remainingMoney -= $allocated;
            }

            return $payment;
        });
    }
}
