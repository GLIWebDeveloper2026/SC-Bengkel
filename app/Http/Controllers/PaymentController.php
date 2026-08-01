<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function bulkForm(Request $request)
    {
        $customers = Customer::where('is_rental_owner', true)
            ->orWhereHas('invoices', function ($q) {
                $q->where('status', '!=', 'paid');
            })
            ->get();

        $selectedCustomerId = $request->query('customer_id');
        $unpaidInvoices = collect();

        if ($selectedCustomerId) {
            $unpaidInvoices = Invoice::where('customer_id', $selectedCustomerId)
                ->where('status', '!=', 'paid')
                ->with(['workOrder.vehicle'])
                ->get();
        }

        return view('payments.bulk', compact('customers', 'selectedCustomerId', 'unpaidInvoices'));
    }

    public function processBulk(Request $request)
    {
        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'total_paid'     => 'required|numeric|gt:0',
            'invoice_ids'    => 'required|array|min:1',
            'invoice_ids.*'  => 'exists:invoices,id',
            'payment_method' => 'required|string',
        ]);

        $payment = $this->paymentService->processBulkPayment(
            $validated['customer_id'],
            $validated['total_paid'],
            $validated['invoice_ids'],
            $validated['payment_method']
        );

        return redirect()->route('payments.bulk', ['customer_id' => $validated['customer_id']])
            ->with('success', 'Pembayaran Bulk ' . $payment->payment_number . ' sebesar Rp ' . number_format($payment->total_paid, 0, ',', '.') . ' berhasil diproses.');
    }

    public function showInvoice($id)
    {
        $invoice = Invoice::with(['workOrder.vehicle.customer', 'workOrder.items.mechanic', 'paymentAllocations.payment'])
            ->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }
}
